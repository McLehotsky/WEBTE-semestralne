from pypdf import PdfMerger, PdfReader, PdfWriter
from fastapi import HTTPException
import tempfile
import zipfile
import os

# 1. Merge two PDFs
def merge_pdfs(file1, file2):
    file1.seek(0)
    file2.seek(0)

    writer = PdfWriter()

    for f in [file1, file2]:
        reader = PdfReader(f)
        writer.append(reader)

    output = tempfile.NamedTemporaryFile(delete=False, suffix=".pdf")
    with open(output.name, "wb") as out_f:
        writer.write(out_f)

    return output.name

# 2. Delete selected pages (comma-separated: "0,2,4")
def delete_pages(file, pages):
    reader = PdfReader(file)
    writer = PdfWriter()
    to_delete = sorted(set(int(p.strip()) for p in pages.split(",")))
    for i in range(len(reader.pages)):
        if i not in to_delete:
            writer.add_page(reader.pages[i])
    output = tempfile.NamedTemporaryFile(delete=False, suffix=".pdf")
    with open(output.name, "wb") as f:
        writer.write(f)
    return output.name

# 3. Reorder pages (e.g., "2,0,1")
def reorder_pages(file, order):
    reader = PdfReader(file)
    writer = PdfWriter()
    sequence = [int(p.strip()) for p in order.split(",")]
    for i in sequence:
        writer.add_page(reader.pages[i])
    output = tempfile.NamedTemporaryFile(delete=False, suffix=".pdf")
    with open(output.name, "wb") as f:
        writer.write(f)
    return output.name

# 4. Extract selected pages (e.g., "0,2")
def extract_pages(file, pages):
    reader = PdfReader(file)
    writer = PdfWriter()
    for p in pages.split(","):
        writer.add_page(reader.pages[int(p.strip())])
    output = tempfile.NamedTemporaryFile(delete=False, suffix=".pdf")
    with open(output.name, "wb") as f:
        writer.write(f)
    return output.name

# 5. Split PDF (return list of file paths)
def split_pdf_to_zip(file, chunk_size):
    reader = PdfReader(file)
    total_pages = len(reader.pages)

    temp_dir = tempfile.mkdtemp()
    pdf_paths = []

    for i in range(0, total_pages, chunk_size):
        writer = PdfWriter()
        for j in range(i, min(i + chunk_size, total_pages)):
            writer.add_page(reader.pages[j])

        part_path = os.path.join(temp_dir, f"part_{i//chunk_size + 1}.pdf")
        with open(part_path, "wb") as f:
            writer.write(f)
        pdf_paths.append(part_path)

    zip_path = tempfile.NamedTemporaryFile(delete=False, suffix=".zip").name
    with zipfile.ZipFile(zip_path, "w") as zipf:
        for path in pdf_paths:
            zipf.write(path, arcname=os.path.basename(path))

    return zip_path

# 6. Rotate selected pages (e.g., pages="0,2", angle=90)
def rotate_pages_individual(file, rotations: str):
    reader = PdfReader(file)
    writer = PdfWriter()

    # Parse string: "0:90,1:-90" → {0: 90, 1: -90}
    rotation_map = {}
    for pair in rotations.split(","):
        if ":" not in pair:
            continue
        page_str, angle_str = pair.split(":")
        rotation_map[int(page_str.strip())] = int(angle_str.strip())

    for i, page in enumerate(reader.pages):
        if i in rotation_map:
            page.rotate(rotation_map[i])
        writer.add_page(page)

    output = tempfile.NamedTemporaryFile(delete=False, suffix=".pdf")
    with open(output.name, "wb") as f:
        writer.write(f)

    return output.name

# 7. Add a page from one PDF into another at a specific position
def add_page(base_file, insert_file, position):
    base_reader = PdfReader(base_file)
    base_pages = base_reader.pages
    insert_reader = PdfReader(insert_file)
    writer = PdfWriter()
    for i in range(len(base_pages)):
        if i == position:
            writer.add_page(insert_reader.pages[0])
        writer.add_page(base_pages[i])
    if position >= len(base_pages):
        writer.add_page(insert_reader.pages[0])
    output = tempfile.NamedTemporaryFile(delete=False, suffix=".pdf")
    with open(output.name, "wb") as f:
        writer.write(f)
    return output.name

# 8. Extract plain text from PDF
def extract_text_from_pdf(file):
    file.seek(0)
    reader = PdfReader(file)
    text = "\n".join([page.extract_text() or "" for page in reader.pages])

    # Ulož text do dočasného .txt súboru
    temp = tempfile.NamedTemporaryFile(delete=False, suffix=".txt", mode="w", encoding="utf-8")
    temp.write(text)
    temp.close()

    return temp.name

# 9. Add password to PDF
def encrypt_pdf(file, password):
    reader = PdfReader(file)
    writer = PdfWriter()
    for page in reader.pages:
        writer.add_page(page)
    writer.encrypt(password)
    output = tempfile.NamedTemporaryFile(delete=False, suffix=".pdf")
    with open(output.name, "wb") as f:
        writer.write(f)
    return output.name

# 10. Remove password from PDF
def decrypt_pdf(file, password):
    reader = PdfReader(file)

    if reader.is_encrypted:
        success = reader.decrypt(password)
        if not success:
            raise HTTPException(
                status_code=401,
                detail="Nesprávne heslo pre dešifrovanie PDF súboru."
            )

    writer = PdfWriter()
    for page in reader.pages:
        writer.add_page(page)

    output = tempfile.NamedTemporaryFile(delete=False, suffix=".pdf")
    with open(output.name, "wb") as f:
        writer.write(f)
        
    return output.name
