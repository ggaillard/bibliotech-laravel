# Workflows à activer (une fois, par l'enseignant)

GitHub refuse qu'une application écrive dans `.github/workflows/`. Les dix workflows de tests
sont donc rangés ici. Pour les activer, depuis le dossier du dépôt sur votre poste :

```bash
git pull
git mv ci-a-copier/workflows .github/workflows
git rm ci-a-copier/README.md
git commit -m "ci: activer les tests par séance et la vérification du style"
git push
```

Faites-le **avant** que les étudiants créent leur dépôt avec « Use this template » :
un dépôt créé avant n'aura pas les workflows.
