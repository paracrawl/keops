


docker build -t keops:latest -f Dockerfile.dbremote .
docker-compose up -d

docker exec -i -u postgres postgres_db createdb keopsdb
cat keopsdb_init.sql | docker exec -i -u postgres postgres_db psql keopsdb

Visit http://localhost:8080