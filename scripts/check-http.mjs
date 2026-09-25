import assert from 'node:assert/strict';
import {randomUUID} from 'node:crypto';

// Exercise real HTTP cookies and CSRF against the optional local demo account.
const base = `http://localhost:${process.env.APP_PORT || 8090}`;
const cookies = new Map();
let csrf = '';
async function request(path, method = 'GET', body) {
  const response = await fetch(`${base}${path}`, {
    method, redirect: 'manual',
    headers: {Accept: 'application/json', 'Content-Type': 'application/json',
      Cookie: [...cookies].map(([k,v]) => `${k}=${v}`).join('; '), 'X-CSRF-TOKEN': csrf},
    body: body === undefined ? undefined : JSON.stringify(body),
  });
  for (const cookie of response.headers.getSetCookie()) {
    const pair = cookie.split(';')[0], split = pair.indexOf('=');
    cookies.set(pair.slice(0,split), pair.slice(split+1));
  }
  const data = await response.json().catch(() => ({}));
  if (data.csrf_token) csrf = data.csrf_token;
  return {response,data};
}
assert.equal((await request('/api/session')).response.status,200);
assert.equal((await request('/api/login','POST',{email:'alex@example.test',password:'hello-there-demo'})).response.status,200);
const alias = `check-${randomUUID().slice(0,8)}`;
const created = await request('/api/links','POST',{title:'HTTP verification',alias,url:'https://example.com/first'});
assert.equal(created.response.status,201);
const {id,version} = created.data.data;
const edited = await request(`/api/links/${id}`,'PATCH',{title:'HTTP verification',url:'https://example.com/second',version});
assert.equal(edited.response.status,200);
assert.equal(edited.data.data.history.length,2);
const redirect = await fetch(`${base}/${alias}`,{redirect:'manual'});
assert.equal(redirect.status,302);
assert.equal(redirect.headers.get('location'),'https://example.com/second');
assert.equal(redirect.headers.get('set-cookie'),null);
assert.equal((await request(`/api/links/${id}`)).data.data.clicks,1);
assert.equal((await request(`/api/links/${id}/status`,'PATCH',{status:'archived',version:edited.data.data.version})).response.status,200);
assert.equal((await fetch(`${base}/${alias}`,{redirect:'manual'})).status,410);
assert.equal((await request('/api/logout','POST')).response.status,200);
assert.equal((await request('/api/links')).response.status,401);
console.log('HTTP session, create/edit/history, redirect, cookie-free analytics, archive and logout passed.');
