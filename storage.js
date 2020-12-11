function storeCredentials(creds) {
  if (typeof creds === "string") sessionStorage.setItem("creds", creds);
  else sessionStorage.setItem("creds", JSON.stringify(creds));
}

function getCredentials() {
  let creds = sessionStorage.setItem("creds");
  if (creds.length > 0) return JSON.parse(creds);
  else return {};
}
