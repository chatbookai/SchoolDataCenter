// create out directory for static Chrome Extension

const fs = require('fs');
const glob = require('glob');

const files = glob.sync('out/**/*.html');
files.forEach((file) => {
  let content = fs.readFileSync(file, 'utf-8');
  content = content.replace(/\/_next/g, './next');
  fs.writeFileSync(file, content, 'utf-8');
});

const sourcePath = 'out/_next';
const destinationPath = 'out/next';

fs.rename(sourcePath, destinationPath, (err) => {
  if (err) {
    console.error('Failed to rename "_next" directory to "next".', err);
  } else {
    console.log('Renamed "_next" directory to "next" successfully.');
  }
});

const sourcePathPopup = 'out/popup/index.html';
const destinationPathPopup = 'out/index.html';

fs.rename(sourcePathPopup, destinationPathPopup, (err) => {
  if (err) {
    console.error('Failed to rename "popup/index.html" directory to "index.html".', err);
  } else {
    console.log('Renamed "popup/index.html" directory to "index.html" successfully.');
  }
});
