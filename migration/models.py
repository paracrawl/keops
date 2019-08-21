import datetime
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy import Table, Column, Integer, String, Enum, ForeignKey
from sqlalchemy.types import TIMESTAMP, Boolean
from sqlalchemy.dialects.postgresql import TSVECTOR
from sqlalchemy.sql.expression import text
from sqlalchemy.orm import relationship

Base = declarative_base()
Base.metadata.schema = 'keopsdb'

roleEnum = Enum('ADMIN', 'STAFF', 'USER', name='role', create_type=True, schema='keopsdb')
taskstatusEnum = Enum('PENDING', 'STARTED', 'DONE', name='taskstatus', create_type=True, schema='keopsdb')
labelEnum = Enum('P','V','L','A','T','MT','E','F', name='label', create_type=True, schema='keopsdb')
modeEnum = Enum('VAL', 'ADE', 'FLU', name='mode', create_type=True, schema='keopsdb')

user_langs = Table('user_langs', Base.metadata,
    Column('id', Integer, primary_key=True),
    Column('user_id', Integer, ForeignKey('users.id'), nullable=False, unique=True),
    Column('lang_id', Integer, ForeignKey('langs.id'), nullable=False, unique=True)
)

sentences_tasks = Table('sentences_tasks', Base.metadata,
    Column('id', Integer, primary_key=True),
    Column('task_id', Integer, ForeignKey('tasks.id'), nullable=False),
    Column('sentence_id', Integer, ForeignKey('sentences.id'), nullable=False),
    Column('evaluation', labelEnum, nullable=False, server_default='P'),
    Column('creation_date', TIMESTAMP, nullable=False, server_default=text('NOW()')),
    Column('completed_date', TIMESTAMP),
    Column('comments', String(5000))
)

class Users(Base):
    __tablename__ = 'users'
    id = Column(Integer, primary_key=True)
    name = Column(String(200), nullable=False)
    email = Column(String(200), nullable=False, unique=True)
    creation_date = Column(TIMESTAMP, nullable=False, server_default=text('NOW()'))
    role = Column(roleEnum, nullable=False, server_default='USER')
    password = Column(String(200), nullable=False)
    active = Column(Boolean, nullable=False, server_default='True')
    tokens = relationship('Tokens')
    langs = relationship('Langs', secondary=user_langs, backref='users')
    projects = relationship('Projects', secondary=user_langs, backref='users')
    tasks = relationship('Tasks')

class Tokens(Base):
    __tablename__ = 'tokens'
    id = Column(Integer, primary_key=True)
    admin = Column(Integer, ForeignKey('users.id'), nullable=False)
    token = Column(String(200), nullable=False, unique=True)
    email = Column(String(200), nullable=False, unique=True)
    date_sent = Column(TIMESTAMP, nullable=False, server_default=text('NOW()'))
    date_used = Column(TIMESTAMP)

class Langs(Base):
    __tablename__ = 'langs'
    id = Column(Integer, primary_key=True)
    langcode = Column(String(5), nullable=False, unique=True)
    langname = Column(String(50), nullable=False, unique=True)
    corpora_source = relationship('Corpora')
    corpora_target = relationship('Corpora')
    tasks_source = relationship('Tasks')
    tasks_target = relationship('Tasks')

class Projects(Base):
    __tablename__ = 'projects'
    id = Column(Integer, primary_key=True)
    owner = Column(Integer, ForeignKey('users.id'), nullable=False)
    name = Column(String(100), nullable=False)
    description = Column(String(500))
    creation_date = Column(TIMESTAMP, nullable=False, server_default=text('NOW()'))
    active = Column(Boolean, nullable=False, server_default='True')
    tasks = relationship('Tasks')

class Corpora(Base):
    __tablename__ = 'corpora'
    id = Column(Integer, primary_key=True)
    name = Column(String(100), nullable=False)
    source_lang = Column(Integer, ForeignKey('langs.id'), nullable=False)
    target_lang = Column(Integer, ForeignKey('langs.id'), nullable=False)
    lines = Column(Integer)
    creation_date = Column(TIMESTAMP, nullable=False, server_default=text('NOW()'))
    active = Column(Boolean, nullable=False, server_default='True')
    tasks = relationship('Tasks')
    sentences = relationship('Sentences')

class Tasks(Base):
    __tablename__ = 'tasks'
    id = Column(Integer, primary_key=True)
    project_id = Column(Integer, ForeignKey('projects.id'), nullable=False)
    assigned_user = Column(Integer, ForeignKey('users.id'))
    corpus_id = Column(Integer, ForeignKey('corpora.id'), nullable=False)
    size = Column(Integer)
    status = Column(taskstatusEnum, nullable=False, server_default='PENDING')
    source_lang = Column(String(5), ForeignKey('langs.langcode'), nullable=False)
    target_lang = Column(String(5), ForeignKey('langs.langcode'), nullable=False)
    creation_date = Column(TIMESTAMP, nullable=False, server_default=text('NOW()'))
    assigned_date = Column(TIMESTAMP)
    completed_date = Column(TIMESTAMP)
    mode = Column(modeEnum)

class Sentences(Base):
    __tablename__ = 'sentences'
    id = Column(Integer, primary_key=True)
    corpus_id = Column(Integer, ForeignKey('corpora.id'), nullable=False)
    source_text = Column(String(5000), nullable=False)
    target_text = Column(String(5000), nullable=False)
    source_text_vector = Column(TSVECTOR)
    target_text_vector = Column(TSVECTOR)
    tasks = relationship('Tasks', secondary=sentences_tasks, backref='tasks')

class Comments(Base):
    __tablename__ = 'comments'
    pair = Column(Integer, ForeignKey('sentences_tasks.id'), primary_key=True)
    name = Column(String, primary_key=True)
    value = Column(String)