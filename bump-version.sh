#!/bin/bash

set -e

if [ $# -ne 1 ]; then
  echo "Usage: `basename $0` <tag>"
  exit 65
fi

TAG=$1

#
# Tag & build master branch
#
git checkout master
git tag ${TAG}
box.phar build

#
# Copy executable file into GH pages
#
git checkout gh-pages

cp beanbox.phar downloads/beanbox-${TAG}.phar
git add downloads/beanbox-${TAG}.phar

SHA1=$(openssl sha1 beanbox.phar)

JSON='name:"beanbox.phar"'
JSON="${JSON},sha1:\"${SHA1}\""
JSON="${JSON},url:\"http://prgtw.github.io/beanstalk-toolbox/downloads/beanbox-${TAG}.phar\""
JSON="${JSON},version:\"${TAG}\""

#
# Update manifest
#
cat manifest.json | jsawk -a "this.push({${JSON}})" | python -mjson.tool > manifest.json.tmp
mv manifest.json.tmp manifest.json
git add manifest.json

git commit -m "Bump version ${TAG}"

#
# Go back to master
#
git checkout master

echo "New version created. Now you should run:"
echo "git push origin gh-pages"
echo "git push origin ${TAG}"
