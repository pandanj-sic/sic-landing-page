#!/bin/bash
set -e

# Setup colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${BLUE}=== Google Cloud Run Deployment Script ===${NC}"

# Check if gcloud is installed
if ! command -v gcloud &> /dev/null; then
    echo -e "${YELLOW}gcloud CLI is not installed or not in PATH. Please install it first.${NC}"
    exit 1
fi

# Check if logged in
ACTIVE_ACCOUNT=$(gcloud config get-value account 2>/dev/null)
if [ -z "$ACTIVE_ACCOUNT" ] || [ "$ACTIVE_ACCOUNT" = "(unset)" ]; then
    echo -e "${YELLOW}No active Google Cloud account detected. Initiating login...${NC}"
    gcloud auth login
fi

# Get current project
PROJECT_ID=$(gcloud config get-value project 2>/dev/null)

if [ -z "$PROJECT_ID" ] || [ "$PROJECT_ID" = "(unset)" ]; then
    echo -e "${YELLOW}No Google Cloud project is currently selected.${NC}"
    read -p "Enter your Google Cloud Project ID: " PROJECT_ID
    gcloud config set project "$PROJECT_ID"
else
    echo -e "${GREEN}Active project: $PROJECT_ID${NC}"
    read -p "Would you like to use this project? (Y/n): " use_current
    if [[ "$use_current" =~ ^[nN]$ ]]; then
        read -p "Enter your Google Cloud Project ID: " PROJECT_ID
        gcloud config set project "$PROJECT_ID"
    fi
fi

# Ask for Region
DEFAULT_REGION="us-central1"
read -p "Enter preferred deployment region [default: $DEFAULT_REGION]: " REGION
REGION=${REGION:-$DEFAULT_REGION}

# Ask for Repository Name
DEFAULT_REPO="sic-landing-page"
read -p "Enter Artifact Registry repository name [default: $DEFAULT_REPO]: " REPO_NAME
REPO_NAME=${REPO_NAME:-$DEFAULT_REPO}

# Ask for Service Name
DEFAULT_SERVICE="sic-landing-page"
read -p "Enter Cloud Run service name [default: $DEFAULT_SERVICE]: " SERVICE_NAME
SERVICE_NAME=${SERVICE_NAME:-$DEFAULT_SERVICE}

echo -e "${BLUE}Enabling Google Cloud Services (Cloud Build, Cloud Run, Artifact Registry)...${NC}"
gcloud services enable \
    artifactregistry.googleapis.com \
    run.googleapis.com \
    cloudbuild.googleapis.com

# Create Artifact Registry if it doesn't exist
echo -e "${BLUE}Checking Artifact Registry Repository...${NC}"
if ! gcloud artifacts repositories describe "$REPO_NAME" --location="$REGION" &>/dev/null; then
    echo -e "${YELLOW}Creating Artifact Registry Repository: $REPO_NAME in $REGION...${NC}"
    gcloud artifacts repositories create "$REPO_NAME" \
        --repository-format=docker \
        --location="$REGION" \
        --description="SIC Landing Page Docker Repository"
else
    echo -e "${GREEN}Artifact Registry repository already exists.${NC}"
fi

# Define the Image Tag
IMAGE_TAG="$REGION-docker.pkg.dev/$PROJECT_ID/$REPO_NAME/$SERVICE_NAME:latest"

echo -e "${BLUE}Submitting build to Cloud Build: $IMAGE_TAG...${NC}"
gcloud builds submit --tag "$IMAGE_TAG" .

# Retrieve app key from .env or fallback
APP_KEY=$(grep '^APP_KEY=' .env | cut -d '=' -f2- || true)
if [ -z "$APP_KEY" ]; then
    echo -e "${YELLOW}Generating new APP_KEY for deployment...${NC}"
    APP_KEY=$(php artisan key:generate --show --no-interaction || true)
fi

if [ -z "$APP_KEY" ]; then
    APP_KEY="base64:kd9hyGEbFXB4QEsTNfxkTsvSufHay1wbvq/O7f+Xu50="
fi

echo -e "${BLUE}Deploying container to Cloud Run...${NC}"
gcloud run deploy "$SERVICE_NAME" \
    --image="$IMAGE_TAG" \
    --region="$REGION" \
    --allow-unauthenticated \
    --set-env-vars="APP_KEY=$APP_KEY,APP_ENV=production,APP_DEBUG=false,DB_CONNECTION=sqlite,DB_DATABASE=/app/database/database.sqlite"

echo -e "${GREEN}=== Deployment Complete! ===${NC}"
echo -e "${GREEN}Your application has been deployed successfully to Cloud Run.${NC}"
