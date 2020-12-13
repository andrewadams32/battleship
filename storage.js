const credsKey = "creds";
function storeCredentials(creds) {
  // obj or stringified obj
  if (typeof creds === "string") sessionStorage.setItem(credsKey, creds);
  else sessionStorage.setItem(credsKey, JSON.stringify(creds));
}

function getCredentials() {
  let creds = sessionStorage.getItem(credsKey);
  if (creds && creds.length > 0) return JSON.parse(creds);
  else return null;
}

function clearCredentials() {
  sessionStorage.removeItem(credsKey);
}
