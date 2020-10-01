"""Conditional lower function

Revision ID: 6086caf1cb18
Revises: ed9f000d6608
Create Date: 2020-10-01 09:15:49.268559

"""
from alembic import op
import sqlalchemy as sa


# revision identifiers, used by Alembic.
revision = '6086caf1cb18'
down_revision = 'ed9f000d6608'
branch_labels = None
depends_on = None


def upgrade():
    op.execute("""
        CREATE OR REPLACE FUNCTION lower_if_text(e anyelement) RETURNS anyelement AS $$
            BEGIN
                IF pg_typeof(e) = 'character varying'::regtype then
                    return lower(e);
                else
                    return e;
                end if;
            END
        $$ LANGUAGE plpgsql;
    """)


def downgrade():
    op.execute("DROP FUNCTION IF EXISTS lower_if_text")
