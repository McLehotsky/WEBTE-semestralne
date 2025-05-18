from sqlalchemy import Column, Integer, String, Text, DateTime, BigInteger, ForeignKey, Enum
from sqlalchemy.orm import relationship
from sqlalchemy.ext.declarative import declarative_base
import datetime
import enum

Base = declarative_base()

class PdfEdit(Base):
    __tablename__ = "pdf_edits"
    
    id = Column(Integer, primary_key=True)
    name = Column(String(255), nullable=False)
    slug = Column(String(255), unique=True, nullable=False)
    description = Column(Text)
    created_at = Column(DateTime, default=datetime.datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.datetime.utcnow, onupdate=datetime.datetime.utcnow)

    # spätná väzba
    edit_histories = relationship("EditHistory", back_populates="pdf_edit")

# Enum pre accessed_via
class AccessSource(enum.Enum):
    frontend = "frontend"
    api = "api"


class EditHistory(Base):
    __tablename__ = "edit_history"

    id = Column(BigInteger, primary_key=True)
    
    user_id = Column(BigInteger, ForeignKey("users.id"), nullable=False)
    pdf_edit_id = Column(BigInteger, ForeignKey("pdf_edits.id"), nullable=False)
    
    accessed_via = Column(Enum(AccessSource), nullable=False)
    
    used_at = Column(DateTime, default=datetime.datetime.utcnow)
    created_at = Column(DateTime, default=datetime.datetime.utcnow)
    updated_at = Column(DateTime, default=datetime.datetime.utcnow, onupdate=datetime.datetime.utcnow)

    # Voliteľné pre jednoduchý prístup k prepojeným dátam
    user = relationship("User", back_populates="edit_histories")
    pdf_edit = relationship("PdfEdit", back_populates="edit_histories")


class User(Base):
    __tablename__ = "users"

    id = Column(BigInteger, primary_key=True)
    name = Column(String(255))
    email = Column(String(255), unique=True, nullable=False)
    password = Column(String(255), nullable=False)

    # prepojenie na edit históriu
    edit_histories = relationship("EditHistory", back_populates="user")
