#! /bin/bash
#push to github

message='maj'
if [[ $1 != '' ]] 
then
    message=$1
fi

echo "message: $message"

current_branch=$(git branch --show-current)
git add .
git commit -m "$message"
git push origin "$current_branch"