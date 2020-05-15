Import-Module ActiveDirectory

$password = ConvertTo-SecureString "7BdGYZ8A1vRZo6pH1PpF" -AsPlainText -Force
$myCreds = New-Object System.Management.Automation.PSCredential("cyril.service", $password)

$groupFile = Get-Content -Path "\\172.16.0.50\tip\group.txt"

if ($groupFile.Count -eq 0) {
    # File is empty, we exit
    exit;
} else {
    foreach($line in $groupFile) {
        $groupData = $line.Split("{,}")
        Remove-ADGroupMember -Identity "VPNG1" -Members $groupData[1] -Credential $myCreds -Server "172.16.0.51" -Confirm:$false
        Remove-ADGroupMember -Identity "VPNG2" -Members $groupData[1] -Credential $myCreds -Server "172.16.0.51" -Confirm:$false
        Remove-ADGroupMember -Identity "VPNG3" -Members $groupData[1] -Credential $myCreds -Server "172.16.0.51" -Confirm:$false
        Add-ADGroupMember -Identity $groupData[0] -Members $groupData[1] -Credential $myCreds -Server "172.16.0.51"
    }
    # Clear the content of the file
    Clear-Content -Path "\\172.16.0.50\tip\group.txt"
    exit;
}
