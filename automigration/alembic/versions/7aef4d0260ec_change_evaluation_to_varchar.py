"""Change evaluation to varchar

Revision ID: 7aef4d0260ec
Revises: c0f84decbdac
Create Date: 2019-09-18 13:18:20.173002

"""
from alembic import op
import sqlalchemy as sa


# revision identifiers, used by Alembic.
revision = '7aef4d0260ec'
down_revision = 'c0f84decbdac'
branch_labels = None
depends_on = None


def upgrade():
    op.execute("""
        alter table keopsdb.sentences_tasks alter column evaluation type varchar(140) using evaluation::text;
    """)


def downgrade():
    op.execute("""
        delete from keopsdb.sentences_tasks where evaluation not in (select unnest(enum_range(NULL::keopsdb.label))::text)
    """)

    op.execute("""
        alter table keopsdb.sentences_tasks alter column evaluation type keopsdb.label using evaluation::keopsdb.label;
    """)
