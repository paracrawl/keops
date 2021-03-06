"""Rename STAFF to PM

Revision ID: e6e3690332da
Revises: 84c03f89212d
Create Date: 2019-09-23 10:08:43.919246

"""
from alembic import op
import sqlalchemy as sa
from sqlalchemy.dialects import postgresql

# revision identifiers, used by Alembic.
revision = 'e6e3690332da'
down_revision = '84c03f89212d'
branch_labels = None
depends_on = None

def upgrade():
    # ### commands auto generated by Alembic - please adjust! ###
    # ### end Alembic commands ###
    op.execute("""
        ALTER TYPE keopsdb.role RENAME VALUE 'STAFF' TO 'PM';
    """)

def downgrade():
    # ### commands auto generated by Alembic - please adjust! ###
    # ### end Alembic commands ###

    op.execute("""
        ALTER TYPE keopsdb.role RENAME VALUE 'PM' TO 'STAFF';
    """)