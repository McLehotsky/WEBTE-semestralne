from fastapi import FastAPI, UploadFile, File, Form, Header, HTTPException, Depends
from fastapi.responses import FileResponse, JSONResponse
from typing import Annotated, Optional
import tempfile
import zipfile
import os
import pdf_utils

app = FastAPI(
    title="PDF Tools API",
    root_path="/api/pdf",
    description="API na spracovanie PDF súborov – spájanie, rozdelenie, otáčanie strán, šifrovanie, extrakcia textu atď.",
    version="1.0.0"
)

def verify_api_key(x_api_key: Annotated[Optional[str], Header()] = None):
    if x_api_key != "SECRET_KEY":
        raise HTTPException(status_code=403, detail="Invalid API Key")

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
    print("merge endpoint called")
    print(f"file1: {file1.filename}, file2: {file2.filename}")
    output_path = pdf_utils.merge_pdfs(file1.file, file2.file)
    return FileResponse(output_path, media_type="application/pdf", filename="merged.pdf")


@app.post("/delete", summary="Delete selected pages", description="Vymaže zadané strany z PDF súboru.")
async def delete(
    file: Annotated[UploadFile, File(description="Vstupný PDF súbor")],
    pages: Annotated[str, Form(description="Strany na vymazanie, napr. '0,2,4'")]
):
    output_path = pdf_utils.delete_pages(file.file, pages)
    return FileResponse(output_path, media_type="application/pdf", filename="deleted.pdf")


@app.post("/reorder", summary="Reorder pages", description="Zmení poradie strán podľa zadanej sekvencie.")
async def reorder(
    file: Annotated[UploadFile, File(description="Vstupný PDF súbor")],
    order: Annotated[str, Form(description="Poradie strán, napr. '2,0,1'")]
):
    output_path = pdf_utils.reorder_pages(file.file, order)
    return FileResponse(output_path, media_type="application/pdf", filename="reordered.pdf")


@app.post("/extract", summary="Extract selected pages", description="Extrahuje vybrané strany do nového PDF súboru.")
async def extract(
    file: Annotated[UploadFile, File(description="Vstupný PDF súbor")],
    pages: Annotated[str, Form(description="Strany na extrakciu, napr. '0,2'")]
):
    output_path = pdf_utils.extract_pages(file.file, pages)
    return FileResponse(output_path, media_type="application/pdf", filename="extracted.pdf")


@app.post("/split", summary="Split PDF into chunks", description="Rozdelí PDF súbor na viacero častí s N stranami a vráti ZIP súbor so všetkými PDF.")
async def split(
    file: Annotated[UploadFile, File(description="Vstupný PDF súbor")],
    chunk_size: Annotated[int, Form(description="Počet strán na jednu časť (napr. 5 = každý výstup má 5 strán)")]
):
    file.file.seek(0)
    zip_path = pdf_utils.split_pdf_to_zip(file.file, chunk_size)
    return FileResponse(zip_path, media_type="application/zip", filename="split_pdf.zip")


@app.post("/rotate", summary="Rotate selected pages individually", description="Otočí zvolené strany s rôznymi uhlami. Formát: '0:90,1:-90'")
async def rotate(
    file: Annotated[UploadFile, File(description="Vstupný PDF súbor")],
    rotations: Annotated[str, Form(description="Strany a uhly, napr. '0:90,1:-90,2:180'")]
):
    output_path = pdf_utils.rotate_pages_individual(file.file, rotations)
    return FileResponse(output_path, media_type="application/pdf", filename="rotated.pdf")


@app.post("/add-page", summary="Add page from one PDF to another", description="Pridá stranu z jedného PDF do druhého.")
async def add_page_endpoint(
    base: Annotated[UploadFile, File(description="Základný PDF súbor")],
    insert: Annotated[UploadFile, File(description="PDF so stranou na vloženie")],
    position: Annotated[int, Form(description="Pozícia, kam sa má strana vložiť (0 = začiatok)")]
):
    output_path = pdf_utils.add_page(base.file, insert.file, position)
    return FileResponse(output_path, media_type="application/pdf", filename="added.pdf")


@app.post("/extract-text", summary="Extract plain text", description="Získa čistý text z PDF súboru.")
async def extract_text(
    file: Annotated[UploadFile, File(description="Vstupný PDF súbor")]
):
    text = pdf_utils.extract_text_from_pdf(file.file)
    return JSONResponse(content={"text": text})


@app.post("/encrypt", summary="Encrypt PDF", description="Zašifruje PDF súbor zadaným heslom.")
async def encrypt(
    file: Annotated[UploadFile, File(description="Vstupný PDF súbor")],
    password: Annotated[str, Form(description="Heslo na šifrovanie")]
):
    output_path = pdf_utils.encrypt_pdf(file.file, password)
    return FileResponse(output_path, media_type="application/pdf", filename="encrypted.pdf")


@app.post("/decrypt", summary="Decrypt PDF", description="Odomkne zašifrovaný PDF súbor pomocou hesla.")
async def decrypt(
    file: Annotated[UploadFile, File(description="Šifrovaný PDF súbor")],
    password: Annotated[str, Form(description="Heslo na odšifrovanie")]
):
    output_path = pdf_utils.decrypt_pdf(file.file, password)
    return FileResponse(output_path, media_type="application/pdf", filename="decrypted.pdf")