"""Add PAR to enum

Revision ID: 5437d1b9ad47
Revises: 6086caf1cb18
Create Date: 2021-11-09 12:45:32.258286

"""
from alembic import op
import sqlalchemy as sa


# revision identifiers, used by Alembic.
revision = '5437d1b9ad47'
down_revision = '6086caf1cb18'
branch_labels = None
depends_on = None


def upgrade():
    op.execute("""
        COMMIT
    """)

    op.execute("""
        ALTER TYPE keopsdb.evalmode ADD VALUE 'PAR';
    """)


def downgrade():
    pass
