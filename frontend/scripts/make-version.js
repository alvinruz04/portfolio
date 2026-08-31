// scripts/make-version.js
import { writeFileSync } from 'fs'
import { execSync } from 'child_process'

function getVersion() {
  try {
    const sha = execSync('git rev-parse --short HEAD').toString().trim()
    const ts = new Date().toISOString()
    return `${ts} ${sha}`
  } catch {
    return new Date().toISOString()
  }
}

writeFileSync('public/version.txt', getVersion() + '\n', 'utf8')
console.log('Wrote public/version.txt')
