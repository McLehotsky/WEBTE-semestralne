from fastapi import FastAPI, UploadFile, File, Form
from fastapi.responses import FileResponse, JSONResponse
import pdf_utils

app = FastAPI()

@app.get("/")
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
            "POST /decrypt": "Remove password from PDF"
        },
        "documentation": "/docs",
        "openapi": "/openapi.json"
    })

@app.post("/merge")
async def merge(file1: UploadFile = File(...), file2: UploadFile = File(...)):
    output_path = pdf_utils.merge_pdfs(file1.file, file2.file)
    return FileResponse(output_path, media_type="application/pdf", filename="merged.pdf")

@app.post("/delete")
async def delete(file: UploadFile = File(...), pages: str = Form(...)):
    output_path = pdf_utils.delete_pages(file.file, pages)
    return FileResponse(output_path, media_type="application/pdf", filename="deleted.pdf")

@app.post("/reorder")
async def reorder(file: UploadFile = File(...), order: str = Form(...)):
    output_path = pdf_utils.reorder_pages(file.file, order)
    return FileResponse(output_path, media_type="application/pdf", filename="reordered.pdf")

@app.post("/extract")
async def extract(file: UploadFile = File(...), pages: str = Form(...)):
    output_path = pdf_utils.extract_pages(file.file, pages)
    return FileResponse(output_path, media_type="application/pdf", filename="extracted.pdf")

@app.post("/split")
async def split(file: UploadFile = File(...)):
    files = pdf_utils.split_pdf(file.file)
    return JSONResponse(content={"message": "PDF split", "files": files})

@app.post("/rotate")
async def rotate(file: UploadFile = File(...), pages: str = Form(...), angle: int = Form(...)):
    output_path = pdf_utils.rotate_pages(file.file, pages, angle)
    return FileResponse(output_path, media_type="application/pdf", filename="rotated.pdf")

@app.post("/add-page")
async def add_page_endpoint(base: UploadFile = File(...), insert: UploadFile = File(...), position: int = Form(...)):
    output_path = pdf_utils.add_page(base.file, insert.file, position)
    return FileResponse(output_path, media_type="application/pdf", filename="added.pdf")

@app.post("/extract-text")
async def extract_text(file: UploadFile = File(...)):
    text = pdf_utils.extract_text_from_pdf(file.file)
    return JSONResponse(content={"text": text})

@app.post("/encrypt")
async def encrypt(file: UploadFile = File(...), password: str = Form(...)):
    output_path = pdf_utils.encrypt_pdf(file.file, password)
    return FileResponse(output_path, media_type="application/pdf", filename="encrypted.pdf")

@app.post("/decrypt")
async def decrypt(file: UploadFile = File(...), password: str = Form(...)):
    output_path = pdf_utils.decrypt_pdf(file.file, password)
    return FileResponse(output_path, media_type="application/pdf", filename="decrypted.pdf")
