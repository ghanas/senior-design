How to Git
==========

Git setup
---------
```
git clone https://github.com/ghanas/TheArtofHiking.git
cd TheArtofHiking
git checkout -b Anthony
git push --set-upstream origin Anthony
```

Push new and modified files to GitHub
-------------------------------------
```
git add -A
git commit -m "Informative message"
git push
```

Pull GitHub master branch into current local branch
---------------------------------------------------
```
git merge origin/master
```

Status commands
---------------
Show what's going on with git, right now.
```
git status
```

Show history of branch
```
git log --graph --oneline
```

Show changes made to files compared to the previos commit
```
git diff
```

Move files from branch to branch
--------------------------------
```
git stash
git checkout BRANCHNAME
git pop
```
