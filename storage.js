function storeCredentials(creds) {
  if (typeof creds === "string") localStorage.setItem("creds", creds);
  else localStorage.setItem("creds", JSON.stringify(creds));
}

function getCredentials(creds) {
  let creds = localStorage.setItem("creds");
  if (creds.length > 0) return JSON.parse(creds);
  else return {};
}
