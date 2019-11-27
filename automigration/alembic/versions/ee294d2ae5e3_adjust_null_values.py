"""Adjust null values

Revision ID: ee294d2ae5e3
Revises: 7aef4d0260ec
Create Date: 2019-09-18 13:28:48.448320

"""
from alembic import op
import sqlalchemy as sa


# revision identifiers, used by Alembic.
revision = 'ee294d2ae5e3'
down_revision = '7aef4d0260ec'
branch_labels = None
depends_on = None


def upgrade():
    # ### commands auto generated by Alembic - please adjust! ###
    op.alter_column('corpora', 'source_lang',
               existing_type=sa.INTEGER(),
               nullable=True,
               schema='keopsdb')
    op.alter_column('tasks', 'target_lang',
               existing_type=sa.VARCHAR(length=5),
               nullable=False,
               schema='keopsdb')
    # ### end Alembic commands ###


def downgrade():
    # ### commands auto generated by Alembic - please adjust! ###
    op.alter_column('tasks', 'target_lang',
               existing_type=sa.VARCHAR(length=5),
               nullable=True,
               schema='keopsdb')
    op.alter_column('corpora', 'source_lang',
               existing_type=sa.INTEGER(),
               nullable=False,
               schema='keopsdb')
    # ### end Alembic commands ###