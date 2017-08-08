# EZCast release and branching strategies

## Branching strategy

### Branches

*FEATURE(S)*  
	Each new change (features, bugfixes, refactors, ...) must be commited into a new branch dedicated to the feature. These branches must start from `dev`.
	Once the feature is complete, it can be merged back to the `dev` branch.
	
*DEV*  
	Dev branch, new features must be merged on `dev`, which should be your usual working branch. Every new features should start from here.
	La branche de devs en cours sur lasquelle se rassemblent toutes les nouvelles features. Pas de nouvelle feature ici, sauf si bugfix en un seul commit.

*STAGING*  
	Staging branch, when preparing a release `dev` should be merged on `staging` to validate changes before merging on master.

*MASTER*  
	Production branch. Every changes here must be stable and ready to be applied on a production server.

### New feature

Every feature should have its own branch. You can switch branch without loosing your change using :  
`git checkout -b your-new-branch`

### Quickbugfixes

In case of quick fixes to apply to production:
Commit the fix as a new feature on the dev branch.  
To apply only this commit on the master branch, you can [Cherry-pick](http://think-like-a-git.net/sections/rebase-from-the-ground-up/cherry-picking-explained.html) the fix. 

## Releases and versioning

EZcast use the classic semantic versioning in the format :  
`MAJOR.MINOR.PATCH`

- Major version is currently 1 and is not used for now.
- Minor version introduces new features, important refactors, or config/database changes.
- Patch version introduces small fixes or refactors only.  

Each new version is followed by a release on the GitHub system, with a changelog explaining the changes. A new release should be done with every changes on the master branch.
### Database versioning

Database version is stored in the database. The php code also knows which database versions it's supposed to work on and will issue a warning upon connection on EZadmin if the versions missmatch.

Every databases changes should be stored in a script in the `sql_updates` folder at the root of the repository. Each patch is targeting specific database versions and will update the database structure and versions to a newer one.  
(more details on the format are yet to define)
