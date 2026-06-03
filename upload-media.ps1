# Upload wp-content/uploads naar museuminamsterdam.nl via FTPS
# Gebruik: .\upload-media.ps1
# Vul je FTP-gegevens in hieronder (of geef ze in als parameter)

param(
    [string]$FtpHost     = "ftp.cvuyu2f87.service.one",
    [string]$FtpUser     = "cvuyu2f87_u",
    [string]$FtpPass     = "",   # Vul hier je wachtwoord in, of geef mee als -FtpPass "..."
    [string]$RemoteBase  = "/customers/8/2/5/cvuyu2f87/webroots/sites/httpdocs/wp-content/uploads",
    [string]$LocalBase   = "$PSScriptRoot\wp-content\uploads"
)

if (-not $FtpPass) {
    $FtpPass = Read-Host "FTP wachtwoord"
}

$credential = New-Object System.Net.NetworkCredential($FtpUser, $FtpPass)

function Ftp-CreateDir($uri) {
    try {
        $req = [System.Net.FtpWebRequest]::Create($uri)
        $req.Credentials = $credential
        $req.EnableSsl = $true
        $req.Method = [System.Net.WebRequestMethods+Ftp]::MakeDirectory
        $req.UsePassive = $true
        $req.KeepAlive = $false
        $req.GetResponse().Dispose()
    } catch { <# map bestaat al #> }
}

function Ftp-Upload($localFile, $remoteUri) {
    $req = [System.Net.FtpWebRequest]::Create($remoteUri)
    $req.Credentials = $credential
    $req.EnableSsl = $true
    $req.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
    $req.UsePassive = $true
    $req.KeepAlive = $false
    $req.UseBinary = $true

    $content = [System.IO.File]::ReadAllBytes($localFile)
    $req.ContentLength = $content.Length

    $stream = $req.GetRequestStream()
    $stream.Write($content, 0, $content.Length)
    $stream.Dispose()
    $req.GetResponse().Dispose()
}

$files = Get-ChildItem -Path $LocalBase -Recurse -File
$total = $files.Count
$done  = 0

Write-Host "Uploaden van $total bestanden naar $FtpHost..."

foreach ($file in $files) {
    $relative  = $file.FullName.Substring($LocalBase.Length).Replace('\', '/')
    $remoteUri = "ftps://$FtpHost$RemoteBase$relative"

    # Zorg dat de map op de server bestaat
    $remoteDir = "ftps://$FtpHost$RemoteBase$([System.IO.Path]::GetDirectoryName($relative).Replace('\','/'))"
    Ftp-CreateDir $remoteDir

    try {
        Ftp-Upload $file.FullName $remoteUri
        $done++
        Write-Host "[$done/$total] $relative"
    } catch {
        Write-Warning "FOUT bij $relative : $_"
    }
}

Write-Host "`nKlaar! $done van $total bestanden geupload."
