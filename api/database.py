from sqlalchemy import create_engine
from sqlalchemy.orm import sessionmaker, declarative_base
import baseurl

# Add baseurl.py, with the following content:
# URL = "mysql+pymysql://username:password@127.0.0.1:3306/tablename"
DATABASE_URL = baseurl.URL

# Vytvorenie engine a session
engine = create_engine(DATABASE_URL, pool_pre_ping=True)
SessionLocal = sessionmaker(bind=engine)

Base = declarative_base()