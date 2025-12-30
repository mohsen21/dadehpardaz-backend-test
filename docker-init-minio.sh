#!/bin/bash
set -e

echo "Waiting for MinIO to be ready..."
until curl -f http://minio:9000/minio/health/live &> /dev/null; do
    echo "MinIO is unavailable - sleeping"
    sleep 2
done

echo "MinIO is ready!"

# Configure MinIO client
mc alias set minio http://minio:9000 ${MINIO_KEY:-minioadmin} ${MINIO_SECRET:-minioadmin}

# Create bucket if it doesn't exist
BUCKET_NAME=${MINIO_BUCKET:-expense-attachments}
if ! mc ls minio/${BUCKET_NAME} &> /dev/null; then
    echo "Creating bucket: ${BUCKET_NAME}"
    mc mb minio/${BUCKET_NAME}
    mc anonymous set download minio/${BUCKET_NAME}
    echo "Bucket ${BUCKET_NAME} created successfully!"
else
    echo "Bucket ${BUCKET_NAME} already exists"
fi

echo "MinIO initialization completed!"

