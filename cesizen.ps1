<#
.SYNOPSIS
    Pilotage des environnements CESIZen sous Windows.
    Équivalent PowerShell du Makefile (utilisé pour la démonstration de soutenance).

.EXAMPLE
    .\cesizen.ps1 aide
    .\cesizen.ps1 dev-up
    .\cesizen.ps1 test
    .\cesizen.ps1 prod-up
#>
param(
    [Parameter(Position = 0)]
    [string]$Commande = 'aide'
)

$ErrorActionPreference = 'Stop'
Set-Location -Path $PSScriptRoot

if (-not (Test-Path '.env.local')) {
    Write-Host "ERREUR : .env.local est absent." -ForegroundColor Red
    Write-Host "Creez-le a partir du gabarit : Copy-Item .env.local.example .env.local" -ForegroundColor Yellow
    exit 1
}

$EnvFile = @('--env-file', '.env.local')
$Dev  = $EnvFile
$Test = $EnvFile + @('-f', 'compose.yaml', '-f', 'compose.test.yaml', '-p', 'cesizen-test')
$Prod = $EnvFile + @('-f', 'compose.yaml', '-f', 'compose.prod.yaml', '-p', 'cesizen-prod')

function Invoke-Compose {
    # Le paramètre ne peut pas s'appeler $Args : c'est une variable automatique
    # de PowerShell, qui serait écrasée et provoquerait un splatting vide.
    param([string[]]$Base, [string[]]$Arguments)
    & docker compose @Base @Arguments
    if ($LASTEXITCODE -ne 0) { throw "docker compose a echoue (code $LASTEXITCODE)" }
}

switch ($Commande) {

    'aide' {
        Write-Host ""
        Write-Host "  Environnements CESIZen" -ForegroundColor Cyan
        Write-Host ""
        Write-Host "  DEVELOPPEMENT" -ForegroundColor Yellow
        Write-Host "    dev-up        Demarre le dev        -> http://localhost:8080 (mails : http://localhost:8025)"
        Write-Host "    dev-down      Arrete le dev"
        Write-Host "    dev-logs      Suit les logs"
        Write-Host "    dev-shell     Ouvre un shell dans le conteneur PHP"
        Write-Host "    dev-migrate   Applique les migrations Doctrine"
        Write-Host "    dev-fixtures  Recharge les donnees de demonstration"
        Write-Host ""
        Write-Host "  TESTS" -ForegroundColor Yellow
        Write-Host "    test          Lance la suite de tests dans un environnement jetable"
        Write-Host "    test-down     Detruit l'environnement de test"
        Write-Host ""
        Write-Host "  PRODUCTION (simulee)" -ForegroundColor Yellow
        Write-Host "    prod-up       Demarre la prod       -> http://localhost:8081"
        Write-Host "    prod-down     Arrete la prod"
        Write-Host "    prod-logs     Suit les logs"
        Write-Host ""
        Write-Host "  QUALITE" -ForegroundColor Yellow
        Write-Host "    lint          Verifie conteneur, templates Twig et fichiers YAML"
        Write-Host "    audit         Recherche les vulnerabilites connues des dependances"
        Write-Host "    ci            Reproduit localement la chaine d'integration continue"
        Write-Host ""
    }

    'dev-up' {
        Invoke-Compose $Dev @('up', '-d', '--build')
        Write-Host "Developpement demarre : http://localhost:8080 - mails : http://localhost:8025" -ForegroundColor Green
    }
    'dev-down'     { Invoke-Compose $Dev @('down') }
    'dev-logs'     { Invoke-Compose $Dev @('logs', '-f') }
    'dev-shell'    { Invoke-Compose $Dev @('exec', 'php', 'sh') }
    'dev-migrate'  { Invoke-Compose $Dev @('exec', 'php', 'php', 'bin/console', 'doctrine:migrations:migrate', '--no-interaction') }
    'dev-fixtures' { Invoke-Compose $Dev @('exec', 'php', 'php', 'bin/console', 'doctrine:fixtures:load', '--no-interaction') }

    'test' {
        Invoke-Compose $Test @('up', '-d', '--build')
        Invoke-Compose $Test @('exec', '-T', 'php', 'php', 'bin/console', 'doctrine:migrations:migrate', '--no-interaction', '--env=test')
        Invoke-Compose $Test @('exec', '-T', 'php', 'vendor/bin/phpunit')
        Write-Host "Suite de tests terminee." -ForegroundColor Green
    }
    'test-down' { Invoke-Compose $Test @('down', '-v') }

    'prod-up' {
        Invoke-Compose $Prod @('up', '-d', '--build')
        Write-Host "Production simulee demarree : http://localhost:8081" -ForegroundColor Green
    }
    'prod-down' { Invoke-Compose $Prod @('down') }
    'prod-logs' { Invoke-Compose $Prod @('logs', '-f') }

    'lint' {
        Invoke-Compose $Dev @('exec', '-T', 'php', 'php', 'bin/console', 'lint:container')
        Invoke-Compose $Dev @('exec', '-T', 'php', 'php', 'bin/console', 'lint:twig', 'templates')
        Invoke-Compose $Dev @('exec', '-T', 'php', 'php', 'bin/console', 'lint:yaml', 'config', '--parse-tags')
    }
    'audit' { Invoke-Compose $Dev @('exec', '-T', 'php', 'composer', 'audit') }

    'ci' {
        & $PSCommandPath lint
        & $PSCommandPath audit
        & $PSCommandPath test
    }

    default {
        Write-Host "Commande inconnue : $Commande" -ForegroundColor Red
        & $PSCommandPath aide
        exit 1
    }
}
