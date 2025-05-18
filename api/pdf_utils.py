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
    to_delete = set(parse_page_selection(pages, len(reader.pages), allow_duplicates=False))

    writer = PdfWriter()
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
    sequence = parse_page_selection(order, len(reader.pages), allow_duplicates=True)

    writer = PdfWriter()
    for i in sequence:
        writer.add_page(reader.pages[i])

    output = tempfile.NamedTemporaryFile(delete=False, suffix=".pdf")
    with open(output.name, "wb") as f:
        writer.write(f)
    return output.name

# 4. Extract selected pages (e.g., "0,2")
def extract_pages(file, pages):
    reader = PdfReader(file)
    sequence = parse_page_selection(pages, len(reader.pages), allow_duplicates=True)

    writer = PdfWriter()
    for i in sequence:
        writer.add_page(reader.pages[i])

    output = tempfile.NamedTemporaryFile(delete=False, suffix=".pdf")
    with open(output.name, "wb") as f:
        writer.write(f)
    return output.name

# 5. Split PDF (return list of file paths)
def split_pdf_to_zip(file, chunk_size):
    if chunk_size <= 0:
        raise HTTPException(status_code=400, detail="Chunk size must be greater than 0")
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

# 6. Rotate selected pages (e.g., rotations:"0:90,1:180")
def rotate_pages_individual(file, rotations: str):
    reader = PdfReader(file)
    rotation_map = parse_rotation_map(rotations, len(reader.pages))

    writer = PdfWriter()
    # Parse string: "0:90,1:-90" → {0: 90, 1: -90}
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

    if position == len(base_pages):
        writer.add_page(insert_reader.pages[0])
    elif position > len(base_pages):
        raise HTTPException(status_code=400, detail=f"Invalid insert position {position}. PDF has only {len(base_pages)} pages.")

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



def parse_page_selection(selection: str, total_pages: int, allow_duplicates=True) -> list[int]:
    if not selection:
        raise HTTPException(status_code=400, detail="No page selection provided.")

    pages = []
    seen = set()

    for part in selection.split(","):
        part = part.strip()
        if "-" in part:
            try:
                start, end = map(int, part.split("-"))
                if start > end:
                    raise ValueError()
            except ValueError:
                raise HTTPException(status_code=400, detail=f"Invalid range format: '{part}'")
            for i in range(start, end + 1):
                if i < 0 or i >= total_pages:
                    raise HTTPException(status_code=400, detail=f"Page index {i} out of range (0–{total_pages - 1})")
                if allow_duplicates or i not in seen:
                    pages.append(i)
                    seen.add(i)
        elif part != "":
            try:
                idx = int(part)
            except ValueError:
                raise HTTPException(status_code=400, detail=f"Invalid page index: '{part}'")
            if idx < 0 or idx >= total_pages:
                raise HTTPException(status_code=400, detail=f"Page index {idx} out of range (0–{total_pages - 1})")
            if allow_duplicates or idx not in seen:
                pages.append(idx)
                seen.add(idx)
    
    return pages

def parse_rotation_map(rotation_string: str, total_pages: int) -> dict[int, int]:
    """
    Parses rotation instructions in the format like:
    '0-2:90,4:-90,6-7:180' → {0: 90, 1: 90, 2: 90, 4: -90, 6: 180, 7: 180}
    """
    if not rotation_string:
        raise HTTPException(status_code=400, detail="No rotation instructions provided.")

    rotation_map: dict[int, int] = {}

    for pair in rotation_string.split(","):
        if ":" not in pair:
            raise HTTPException(status_code=400, detail=f"Invalid rotation format: '{pair}'")
        
        range_part, angle_part = pair.split(":", 1)
        try:
            angle = int(angle_part.strip())
        except ValueError:
            raise HTTPException(status_code=400, detail=f"Invalid rotation angle: '{angle_part}'")

        # parse the range: "2-4" or single "3"
        range_part = range_part.strip()
        if "-" in range_part:
            try:
                start, end = map(int, range_part.split("-"))
                if start > end:
                    raise ValueError()
            except ValueError:
                raise HTTPException(status_code=400, detail=f"Invalid page range: '{range_part}'")
            indices = list(range(start, end + 1))
        else:
            try:
                indices = [int(range_part)]
            except ValueError:
                raise HTTPException(status_code=400, detail=f"Invalid page index: '{range_part}'")

        for idx in indices:
            if idx < 0 or idx >= total_pages:
                raise HTTPException(status_code=400, detail=f"Page index {idx} out of range (0 to {total_pages - 1})")
            rotation_map[idx] = angle  # last one wins if repeated

    return rotation_map
