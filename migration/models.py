import datetime
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy import Table, Column, Integer, String, Enum, ForeignKey, func
from sqlalchemy.types import TIMESTAMP, Boolean, Numeric
from sqlalchemy.dialects.postgresql import TSVECTOR
from sqlalchemy.sql.expression import text
from sqlalchemy.orm import relationship
from sqlalchemy.schema import MetaData

meta = MetaData(naming_convention = {
    "ix": "%(table_name)s_%(column_0_name)s_idx",
    "uq": "%(table_name)s_%(column_0_name)s_key",
    "ck": "%(table_name)s_%(constraint_name)s_check",
    "fk": "%(table_name)s_%(column_0_name)s_fkey",
    "pk": "%(table_name)s_pkey"
}, schema = 'keopsdb')

Base = declarative_base(metadata=meta)

roleEnum = ENUM('ADMIN', 'PM', 'USER', name='role', create_type=True, schema='keopsdb')
taskstatusEnum = Enum('PENDING', 'STARTED', 'DONE', name='taskstatus', create_type=True, schema='keopsdb')
labelEnum = Enum('P','V','L','A','T','MT','E','F', name='label', create_type=True, schema='keopsdb')
modeEnum = Enum('VAL', 'ADE', 'FLU', 'RAN', name='evalmode', create_type=True, schema='keopsdb')

user_langs = Table('user_langs', Base.metadata,
    Column('id', Integer, primary_key = True),
    Column('user_id', Integer, ForeignKey('users.id'), nullable=False),
    Column('lang_id', Integer, ForeignKey('langs.id'))
)

sentences_tasks = Table('sentences_tasks', Base.metadata,
    Column('id', Integer, primary_key = True),
    Column('task_id', Integer, ForeignKey('tasks.id'), nullable=False),
    Column('sentence_id', Integer, ForeignKey('sentences.id'), nullable=False),
    Column('evaluation', String(140), nullable=False, server_default='P'),
    Column('creation_date', TIMESTAMP, nullable=False, server_default='CURRENT_TIMESTAMP'),
    Column('completed_date', TIMESTAMP),
    Column('time', Numeric)
)

sentences_pairing = Table('sentences_pairing', Base.metadata,
    Column('id_1', Integer, ForeignKey('sentences.id'), primary_key=True),
    Column('id_2', Integer, ForeignKey('sentences.id'), primary_key=True)
)

class Users(Base):
    __tablename__ = 'users'
    id = Column(Integer, primary_key=True)
    name = Column(String(200), nullable=False)
    email = Column(String(200), unique=True, nullable=False)
    creation_date = Column(TIMESTAMP, nullable=False, server_default='CURRENT_TIMESTAMP')
    role = Column(roleEnum, nullable=False, server_default='USER')
    password = Column(String(200), nullable=False)
    active = Column(Boolean, nullable=False, server_default='TRUE')

    tokens_rel = relationship("Tokens", back_populates="users_rel")
    langs_rel = relationship("Langs", secondary=user_langs, back_populates="users_rel")
    projects_rel = relationship('Projects', back_populates='users_rel')
    tasks_rel = relationship('Tasks', back_populates='users_rel')
    feedback_rel = relationship('Tasks', back_populates='users_rel')

class Tokens(Base):
    __tablename__ = 'tokens'
    id = Column(Integer, primary_key=True)
    admin = Column(Integer, ForeignKey('users.id'), nullable=False)
    token = Column(String(200), unique=True, nullable=False)
    email = Column(String(200), unique=True, nullable=False)
    date_sent = Column(TIMESTAMP, nullable=False, server_default='CURRENT_TIMESTAMP')
    date_used = Column(TIMESTAMP)

    users_rel = relationship("Users", back_populates="tokens_rel")

class Langs(Base):
    __tablename__ = 'langs'
    id = Column(Integer, primary_key=True)
    langcode = Column(String(5), unique=True, nullable=False)
    langname = Column(String(50), unique=True, nullable=False)

    users_rel = relationship("Users", secondary=user_langs, back_populates="langs_rel")
    tasks_rel_1 = relationship("Tasks", back_populates="langs_rel_1")
    tasks_rel_2 = relationship("Tasks", back_populates="langs_rel_2")
    corpora_rel_1 = relationship("Corpora", back_populates="langs_rel_1")
    corpora_rel_2 = relationship("Corpora", back_populates="langs_rel_2")

class Projects(Base):
    __tablename__ = 'projects'
    id = Column(Integer, primary_key=True)
    owner = Column(Integer, ForeignKey('users.id'), nullable=False)
    name = Column(String(100), nullable=False)
    description = Column(String(500))
    creation_date = Column(TIMESTAMP, nullable=False, server_default='CURRENT_TIMESTAMP')
    active = Column(Boolean, nullable=False, server_default='TRUE')

    users_rel = relationship('Users', back_populates='projects_rel')
    tasks_rel = relationship('Tasks', back_populates='projects_rel')

class Corpora(Base):
    __tablename__ = 'corpora'
    id = Column(Integer, primary_key=True)
    name = Column(String(100), nullable=False)
    source_lang = Column(Integer, ForeignKey('langs.id'), nullable=True)
    target_lang = Column(Integer, ForeignKey('langs.id'), nullable=False)
    lines = Column(Integer)
    creation_date = Column(TIMESTAMP, nullable=False, server_default='CURRENT_TIMESTAMP')
    active = Column(Boolean, nullable=False, server_default='TRUE')
    evalmode = Column(modeEnum, nullable=False, server_default='VAL'),

    langs_rel_1 = relationship('Langs', back_populates='corpora_rel_1')
    langs_rel_2 = relationship('Langs', back_populates='corpora_rel_2')
    tasks_rel = relationship('Tasks', back_populates='corpora_rel')
    sentences_rel = relationship('Sentences', back_populates='corpora_rel')

class Tasks(Base):
    __tablename__ = 'tasks'
    id = Column(Integer, primary_key=True)
    project_id = Column(Integer, ForeignKey('projects.id'), nullable=False)
    assigned_user = Column(Integer, ForeignKey('users.id'))
    corpus_id = Column(Integer, ForeignKey('corpora.id'), nullable=False)
    size = Column(Integer)
    status = Column(taskstatusEnum, nullable=False, server_default='PENDING')
    creation_date = Column(TIMESTAMP, nullable=False, server_default='CURRENT_TIMESTAMP')
    assigned_date = Column(TIMESTAMP)
    completed_date = Column(TIMESTAMP)
    source_lang = Column(String(5), ForeignKey('langs.langcode'), nullable=True)
    target_lang = Column(String(5), ForeignKey('langs.langcode'), nullable=False)
    evalmode = Column(modeEnum, nullable=False, server_default='VAL'),
    score = Column(Numeric)

    projects_rel = relationship('Projects', back_populates='tasks_rel')
    users_rel = relationship('Users', back_populates='tasks_rel')
    corpora_rel = relationship('Corpora', back_populates='tasks_rel')
    sentences_rel = relationship("Sentences", secondary=sentences_tasks, back_populates="tasks_rel")
    langs_rel_1 = relationship('Langs', back_populates='tasks_rel_1')
    langs_rel_2 = relationship('Langs', back_populates='tasks_rel_2')
    feedback_rel = relationship('Feedback', back_populates='tasks_rel')

class Sentences(Base):
    __tablename__ = 'sentences'
    id = Column(Integer, primary_key=True)
    corpus_id = Column(Integer, ForeignKey('corpora.id'), nullable=False)
    source_text = Column(String(5000), nullable=False)
    source_text_vector = Column(TSVECTOR)
    type = Column(String(140))
    is_source = Column(Boolean)
    system = Column(String(140))

    corpora_rel = relationship('Corpora', back_populates='sentences_rel')
    tasks_rel = relationship("Tasks", secondary=sentences_tasks, back_populates="sentences_rel")
    sentences_pairing_rel_1 = relationship("Sentences", secondary=sentences_tasks, back_populates="sentences_pairing_rel_2")
    sentences_pairing_rel_2 = relationship("Sentences", secondary=sentences_tasks, back_populates="sentences_pairing_rel_1")

class Comments(Base):
    __tablename__ = 'comments'
    pair = Column(Integer, ForeignKey('sentences_tasks.id'), primary_key=True)
    name = Column(String(140), primary_key=True)
    value = Column(String(255))

    sentences_tasks_rel = relationship('sentences_tasks')

class Feedback(Base):
    __tablename__ = 'feedback'
    id = Column(Integer, primary_key=True)
    score = Column(Integer, nullable=False)
    comments = Column(String(240))
    created = Column(TIMESTAMP, nullable=False, server_default=func.current_timestamp())
    task_id = Column(Integer, ForeignKey('tasks.id'), nullable=False)
    user_id = Column(Integer, ForeignKey('users.id'), nullable=False)

    tasks_rel = relationship('Tasks', back_populates='feedback_rel')
    users_rel = relationship('Tasks', back_populates='feedback_rel')