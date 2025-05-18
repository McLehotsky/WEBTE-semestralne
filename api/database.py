from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker, declarative_base

# Pripojenie cez meno kontajnera, nie localhost!
DATABASE_URL = "mysql+pymysql://user:pass@mysql:3306/pdfapp"

# Vytvorenie engine a session
engine = create_engine(DATABASE_URL, pool_pre_ping=True)
SessionLocal = sessionmaker(bind=engine)

Base = declarative_base()