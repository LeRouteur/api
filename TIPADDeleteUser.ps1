Import-Module ActiveDirectory

$password = ConvertTo-SecureString "7BdGYZ8A1vRZo6pH1PpF" -AsPlainText -Force
$myCreds = New-Object System.Management.Automation.PSCredential("cyril.service", $password)

$userFile = Get-Content -Path "\\172.16.0.50\tip\delete.txt"

if ($userFile.Count -eq 0) {
    # File is empty, we exit
    exit;
} else {
    foreach($line in $userFile) {
        Remove-ADUser -Identity $line -Credential $myCreds -Server "172.16.0.51" - Confirm:$false
    }
    # Clear the content of the file
    Clear-Content -Path "\\172.16.0.50\tip\delete.txt" -Force
    exit;
}