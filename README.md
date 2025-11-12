# phplogin (dstreetwear)

This is a local PHP/XAMPP project (dstreetweardatabase) containing login, registration, admin, and order pages.

What I added
- `.gitignore` — excludes local XAMPP data, uploads, secrets and common IDE files.

How to upload this project to your GitHub repository named `tskhck`

Choose one of the methods below and run the commands from PowerShell in the project root `C:\xampp\htdocs\phplogin`.

1) Create remote repo on GitHub (HTTPS) and push

- Create an empty repository on GitHub named `tskhck` (https://github.com/<your-username>/tskhck).
- Then run these commands locally:

```powershell
cd 'C:\xampp\htdocs\phplogin'
# initialize git if not already
git init
git add .
git commit -m "Initial commit"
# add remote (HTTPS). Replace USERNAME with your GitHub username
git remote add origin https://github.com/USERNAME/tskhck.git
# push (you will be prompted for GitHub credentials or a PAT)
git branch -M main
git push -u origin main
```

2) Create remote repo using SSH (recommended if you have SSH keys set up)

```powershell
cd 'C:\xampp\htdocs\phplogin'
git init
git add .
git commit -m "Initial commit"
# add remote (SSH). Replace USERNAME with your GitHub username
git remote add origin git@github.com:USERNAME/tskhck.git
git branch -M main
git push -u origin main
```

3) Create and push using GitHub CLI (if installed)

```powershell
cd 'C:\xampp\htdocs\phplogin'
gh repo create USERNAME/tskhck --public --source=. --remote=origin --push
```

Notes and safety
- I excluded `C:\xampp\mysql\data` and `/uploads/` in `.gitignore` to avoid uploading local database files or user uploads.
- If you have sensitive config files (like `connection.php` with credentials), review before pushing. Consider moving credentials to an environment file `.env` (excluded) and adding a sample `config.example.php`.

If you want, I can:
- Create a `config.example.php` that documents the DB variables (without secrets).
- Walk you through the exact PowerShell commands and help you set up SSH keys or a personal access token (PAT) for pushing.
