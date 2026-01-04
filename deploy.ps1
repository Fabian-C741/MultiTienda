# 🚀 PowerShell Deploy Script for MultiTienda
# Usage: .\deploy.ps1 [staging|production]

param(
    [Parameter(Position=0)]
    [ValidateSet("staging", "production")]
    [string]$Environment = "staging"
)

$ErrorActionPreference = "Stop"

Write-Host "🚀 MultiTienda Deployment Script" -ForegroundColor Blue
Write-Host "Environment: $Environment" -ForegroundColor Yellow
Write-Host ""

# Pre-deployment checks
Write-Host "📋 Running pre-deployment checks..." -ForegroundColor Blue

# Check current branch
if ($Environment -eq "production") {
    $ExpectedBranch = "main"
    $RemoteUrl = "multitienda.kcrsf.com"
} else {
    $ExpectedBranch = "staging"
    $RemoteUrl = "staging.multitienda.kcrsf.com"
}

$CurrentBranch = git branch --show-current
if ($CurrentBranch -ne $ExpectedBranch) {
    Write-Host "⚠️ Warning: You're on branch '$CurrentBranch' but deploying to '$Environment'" -ForegroundColor Yellow
    Write-Host "Expected branch: '$ExpectedBranch'" -ForegroundColor Yellow
    $Confirm = Read-Host "Continue anyway? (y/N)"
    if ($Confirm -ne "y" -and $Confirm -ne "Y") {
        Write-Host "❌ Deployment cancelled" -ForegroundColor Red
        exit 1
    }
}

# Run health check
if (Test-Path "health-check.php") {
    Write-Host "🧪 Running health check..." -ForegroundColor Blue
    $HealthResult = php health-check.php
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Health check passed" -ForegroundColor Green
    } else {
        Write-Host "❌ Health check failed" -ForegroundColor Red
        Write-Host $HealthResult
        exit 1
    }
} else {
    Write-Host "⚠️ health-check.php not found, skipping health check" -ForegroundColor Yellow
}

# Create deployment package
$Timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
$DeployDir = "deploy_${Environment}_${Timestamp}"
Write-Host "📦 Creating deployment package: $DeployDir" -ForegroundColor Blue

# Create deploy directory
New-Item -ItemType Directory -Path $DeployDir -Force | Out-Null

# Copy files
$ExcludePatterns = @(
    "node_modules",
    ".git",
    "tests",
    "*.log",
    "deploy_*",
    ".env.example"
)

Get-ChildItem -Path . -Recurse | Where-Object {
    $item = $_
    -not ($ExcludePatterns | Where-Object { $item.FullName -like "*$_*" })
} | ForEach-Object {
    $relativePath = $_.FullName.Substring((Get-Location).Path.Length + 1)
    $destPath = Join-Path $DeployDir $relativePath
    $destDir = Split-Path $destPath -Parent
    
    if (-not (Test-Path $destDir)) {
        New-Item -ItemType Directory -Path $destDir -Force | Out-Null
    }
    
    if (-not $_.PSIsContainer) {
        Copy-Item $_.FullName $destPath
    }
}

Write-Host "✅ Deployment package created" -ForegroundColor Green

# Production confirmation
if ($Environment -eq "production") {
    Write-Host "⚠️ PRODUCTION DEPLOYMENT" -ForegroundColor Red
    Write-Host "This will deploy to: $RemoteUrl" -ForegroundColor Yellow
    $Confirm = Read-Host "Are you absolutely sure? Type 'YES' to continue"
    if ($Confirm -ne "YES") {
        Write-Host "❌ Production deployment cancelled" -ForegroundColor Red
        exit 1
    }
}

Write-Host "📤 Deployment package ready for upload" -ForegroundColor Blue
Write-Host "Upload contents of '$DeployDir' to Hostinger" -ForegroundColor Yellow
Write-Host "Target URL: https://$RemoteUrl" -ForegroundColor Green

Write-Host "🎉 Deployment preparation completed!" -ForegroundColor Green