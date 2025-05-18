from fastapi import FastAPI, UploadFile, File, Form, Header, HTTPException, Depends, Security
from fastapi.responses import FileResponse, JSONResponse
from fastapi.openapi.models import APIKey, APIKeyIn, SecuritySchemeType
from fastapi.security.api_key import APIKeyHeader
from fastapi.openapi.utils import get_openapi
from typing import Annotated, Optional
import tempfile
import zipfile
import os
import pdf_utils
import logging
from pypdf import PdfReader
import re

logging.basicConfig(
    filename="debug.log",  # súbor, kam sa budú logy ukladať
    level=logging.DEBUG,   # nastav úroveň (DEBUG, INFO, WARNING, ...)
    format="%(asctime)s - %(levelname)s - %(message)s"
)

app = FastAPI(
    title="PDF Tools API",
    root_path="/api/pdf",
    description="API na spracovanie PDF súborov – spájanie, rozdelenie, otáčanie strán, šifrovanie, extrakcia textu atď.",
    version="1.0.0"
)

def verify_api_key(x_api_key: Annotated[Optional[str], Header()] = None):
    if x_api_key != "SECRET_KEY":
        raise HTTPException(status_code=403, detail="Invalid API Key")
def validate_pdf(file: UploadFile):
    if file.content_type != "application/pdf":
        raise HTTPException(status_code=400, detail="Uploaded file is not a PDF")

    try:
        file.file.seek(0)
        reader = PdfReader(file.file)
        file.file.seek(0)  # reset file pointer
        return reader
    except Exception as e:
        raise HTTPException(status_code=400, detail="Uploaded file is not a valid PDF")
    
def validate_not_encrypted(reader: PdfReader):
    if reader.is_encrypted:
        raise HTTPException(status_code=400, detail="Encrypted PDF is not allowed for this operation")
    
def validate_pdf_upload(file: UploadFile, allow_encrypted: bool = False) -> PdfReader:
    reader = validate_pdf(file)
    if not allow_encrypted:
        validate_not_encrypted(reader)
    return reader


MAX_FILE_SIZE = "20MB"

def parse_size(size_str: str) -> int:
    """
    Konvertuje čitateľné stringy ako '20MB', '100kb', '1.5GB' na bajty (int)
    """
    size_str = size_str.strip().upper()
    match = re.match(r"^(\d+(?:\.\d+)?)\s*(B|KB|MB|GB)$", size_str)
    if not match:
        raise ValueError("Invalid size format. Use formats like '20MB', '100KB', etc.")
    
    number, unit = match.groups()
    factor = {
        "B": 1,
        "KB": 1024,
        "MB": 1024 ** 2,
        "GB": 1024 ** 3,
    }[unit]
    return int(float(number) * factor)

def human_readable_size(bytes: int) -> str:
    """
    Vráti veľkosť v čitateľnom formáte, napr. 20971520 → "20 MB"
    """
    for unit in ['B', 'KB', 'MB', 'GB']:
        if bytes < 1024.0 or unit == 'GB':
            return f"{bytes:.1f} {unit}" if unit != 'B' else f"{int(bytes)} B"
        bytes /= 1024.0

def validate_total_upload_size(*files: UploadFile):
    """
    Overí, že súčet veľkostí všetkých nahraných súborov neprekračuje daný limit.
    """
    max_bytes = parse_size(MAX_FILE_SIZE)
    total_bytes = 0

    for file in files:
        file.file.seek(0, 2)  # presuň na koniec
        size = file.file.tell()
        file.file.seek(0)     # reset späť
        total_bytes += size

    if total_bytes > max_bytes:
        raise HTTPException(
            status_code=413,
            detail=(
                f"The size of uploaded files is too big "
                f"({human_readable_size(total_bytes)} > {MAX_FILE_SIZE})"
            )
        )


@app.get("/", summary="Root endpoint")
def root():
    return JSONResponse(content={
        "available_endpoints": {
            "POST /merge": "Merge two PDF files",
            "POST /delete": "Delete pages from PDF",
            "POST /reorder": "Reorder pages in PDF",
            "POST /extract": "Extract selected pages from PDF",
            "POST /split": "Split PDF into separate pages",
            "POST /rotate": "Rotate selected pages",
            "POST /add-page": "Add a page from one PDF into another",
            "POST /extract-text": "Extract plain text from PDF",
            "POST /encrypt": "Add password to PDF",
            "POST /decrypt": "Remove password from PDF",
            "Fungje": "Ano"
        },
        "documentation": "/docs",
        "openapi": "/openapi.json"
    })

@app.post("/merge", summary="Merge two PDF files", description="Spojí dva PDF súbory do jedného.")
async def merge(
    file1: Annotated[UploadFile, File(description="Prvý PDF súbor")],
    file2: Annotated[UploadFile, File(description="Druhý PDF súbor")]
):
    reader1 = validate_pdf_upload(file1)
    reader2 = validate_pdf_upload(file2)
    validate_total_upload_size(file1, file2)

    output_path = pdf_utils.merge_pdfs(file1.file, file2.file)
    return FileResponse(output_path, media_type="application/pdf", filename="merged.pdf")


@app.post("/delete", summary="Delete selected pages", description="Vymaže zadané strany z PDF súboru.")
async def delete(
    file: Annotated[UploadFile, File(description="Vstupný PDF súbor")],
    pages: Annotated[str, Form(description="Strany na vymazanie, napr. '0,2,4'")]
):
    reader = validate_pdf_upload(file)
    validate_total_upload_size(file)

    output_path = pdf_utils.delete_pages(file.file, pages)
    return FileResponse(output_path, media_type="application/pdf", filename="deleted.pdf")


@app.post("/reorder", summary="Reorder pages", description="Zmení poradie strán podľa zadanej sekvencie.")
async def reorder(
    file: Annotated[UploadFile, File(description="Vstupný PDF súbor")],
    order: Annotated[str, Form(description="Poradie strán, napr. '2,0,1'")]
):
    reader = validate_pdf_upload(file)
    validate_total_upload_size(file)

    output_path = pdf_utils.reorder_pages(file.file, order)
    return FileResponse(output_path, media_type="application/pdf", filename="reordered.pdf")


@app.post("/extract", summary="Extract selected pages", description="Extrahuje vybrané strany do nového PDF súboru.")
async def extract(
    file: Annotated[UploadFile, File(description="Vstupný PDF súbor")],
    pages: Annotated[str, Form(description="Strany na extrakciu, napr. '0,2'")]
):
    reader = validate_pdf_upload(file)
    validate_total_upload_size(file)

    output_path = pdf_utils.extract_pages(file.file, pages)
    return FileResponse(output_path, media_type="application/pdf", filename="extracted.pdf")


@app.post("/split", summary="Split PDF into chunks", description="Rozdelí PDF súbor na viacero častí s N stranami a vráti ZIP súbor so všetkými PDF.")
async def split(
    file: Annotated[UploadFile, File(description="Vstupný PDF súbor")],
    chunk_size: Annotated[int, Form(description="Počet strán na jednu časť (napr. 5 = každý výstup má 5 strán)")]
):
    reader = validate_pdf_upload(file)
    validate_total_upload_size(file)

    zip_path = pdf_utils.split_pdf_to_zip(file.file, chunk_size)
    return FileResponse(zip_path, media_type="application/zip", filename="split_pdf.zip")


@app.post("/rotate", summary="Rotate selected pages individually", description="Otočí zvolené strany s rôznymi uhlami. Formát: '0:90,1:-90'")
async def rotate(
    file: Annotated[UploadFile, File(description="Vstupný PDF súbor")],
    rotations: Annotated[str, Form(description="Strany a uhly, napr. '0:90,1:-90,2:180'")]
):
    reader = validate_pdf_upload(file)
    validate_total_upload_size(file)

    output_path = pdf_utils.rotate_pages_individual(file.file, rotations)
    return FileResponse(output_path, media_type="application/pdf", filename="rotated.pdf")


@app.post("/add-page", summary="Add page from one PDF to another", description="Pridá stranu z jedného PDF do druhého.")
async def add_page_endpoint(
    base: Annotated[UploadFile, File(description="Základný PDF súbor")],
    insert: Annotated[UploadFile, File(description="PDF so stranou na vloženie")],
    position: Annotated[int, Form(description="Pozícia, kam sa má strana vložiť (0 = začiatok)")]
):
    reader1 = validate_pdf_upload(base)
    reader2 = validate_pdf_upload(insert)
    validate_total_upload_size(base, insert)

    output_path = pdf_utils.add_page(base.file, insert.file, position)
    return FileResponse(output_path, media_type="application/pdf", filename="added.pdf")


@app.post("/extract-text", summary="Extract plain text", description="Získa čistý text z PDF súboru.")
async def extract_text(
    file: Annotated[UploadFile, File(description="Vstupný PDF súbor")]
):
    reader = validate_pdf_upload(file)
    validate_total_upload_size(file)

    output_path = pdf_utils.extract_text_from_pdf(file.file)
    return FileResponse(output_path, media_type="text/plain", filename="extracted.txt")


@app.post("/encrypt", summary="Encrypt PDF", description="Zašifruje PDF súbor zadaným heslom.")
async def encrypt(
    file: Annotated[UploadFile, File(description="Vstupný PDF súbor")],
    password: Annotated[str, Form(description="Heslo na šifrovanie")]
):
    reader = validate_pdf_upload(file)
    validate_total_upload_size(file)

    output_path = pdf_utils.encrypt_pdf(file.file, password)
    return FileResponse(output_path, media_type="application/pdf", filename="encrypted.pdf")


@app.post("/decrypt", summary="Decrypt PDF", description="Odomkne zašifrovaný PDF súbor pomocou hesla.")
async def decrypt(
    file: Annotated[UploadFile, File(description="Šifrovaný PDF súbor")],
    password: Annotated[str, Form(description="Heslo na odšifrovanie")]
):
    reader = validate_pdf_upload(file, True)
    validate_total_upload_size(file)

    output_path = pdf_utils.decrypt_pdf(file.file, password)
    return FileResponse(output_path, media_type="application/pdf", filename="decrypted.pdf")