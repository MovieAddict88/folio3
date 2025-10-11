import express from 'express';
import cors from 'cors';
import fs from 'fs';
import path from 'path';
import morgan from 'morgan';
import axios from 'axios';
import { fileURLToPath } from 'url';

const app = express();
const PORT = process.env.PORT || 3000;
app.use(cors());
app.use(express.json());
app.use(morgan('dev'));

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const dataDir = path.join(__dirname, 'data');
const usersFile = path.join(dataDir, 'users.json');

function ensureDataFiles() {
  fs.mkdirSync(dataDir, { recursive: true });
  if (!fs.existsSync(usersFile)) {
    fs.writeFileSync(usersFile, '[]', 'utf-8');
  }
}

function readUsers() {
  try {
    const content = fs.readFileSync(usersFile, 'utf-8');
    return JSON.parse(content);
  } catch (err) {
    return [];
  }
}

function writeUsers(users) {
  fs.writeFileSync(usersFile, JSON.stringify(users, null, 2));
}

ensureDataFiles();

app.get('/api/health', (req, res) => {
  res.json({ status: 'ok' });
});

// Provide mock channels for demo purposes
app.get('/api/mock-channels', (req, res) => {
  try {
    const channels = JSON.parse(
      fs.readFileSync(path.join(__dirname, 'data', 'sample_channels.json'), 'utf-8')
    );
    res.json(channels);
  } catch (e) {
    res.json([]);
  }
});

app.get('/api/users', (req, res) => {
  res.json(readUsers());
});

app.post('/api/users', (req, res) => {
  const { name, mac, portalUrl } = req.body || {};
  if (!name || !mac || !portalUrl) {
    return res.status(400).json({ error: 'name, mac, portalUrl required' });
  }
  const macNorm = String(mac).trim().toUpperCase();
  const macRegex = /^[0-9A-F]{2}([:-]?[0-9A-F]{2}){5}$/;
  if (!macRegex.test(macNorm)) {
    return res.status(400).json({ error: 'Invalid MAC format' });
  }
  const users = readUsers();
  const id = Date.now().toString(36);
  const user = {
    id,
    name: String(name).trim(),
    mac: macNorm.replace(/-/g, ':'),
    portalUrl: String(portalUrl).trim(),
    createdAt: new Date().toISOString()
  };
  users.push(user);
  writeUsers(users);
  res.status(201).json(user);
});

app.delete('/api/users/:id', (req, res) => {
  const users = readUsers();
  const nextUsers = users.filter((u) => u.id !== req.params.id);
  if (nextUsers.length === users.length) {
    return res.status(404).json({ error: 'Not found' });
  }
  writeUsers(nextUsers);
  res.json({ ok: true });
});

app.get('/api/users/:id', (req, res) => {
  const user = readUsers().find((u) => u.id === req.params.id);
  if (!user) {
    return res.status(404).json({ error: 'Not found' });
  }
  res.json(user);
});

app.post('/api/users/:id/validate', async (req, res) => {
  const user = readUsers().find((u) => u.id === req.params.id);
  if (!user) {
    return res.status(404).json({ error: 'Not found' });
  }
  try {
    const url = new URL(user.portalUrl);
    const response = await axios.get(url.toString(), {
      timeout: 5000,
      validateStatus: () => true
    });
    res.json({ reachable: true, status: response.status });
  } catch (err) {
    res.json({ reachable: false, error: err.message });
  }
});

const allowProxy = process.env.ENABLE_PROXY !== 'false';
app.get('/api/proxy', async (req, res) => {
  if (!allowProxy) return res.status(403).json({ error: 'Proxy disabled' });
  const url = req.query.url;
  if (!url) return res.status(400).json({ error: 'url required' });
  try {
    const target = new URL(url);
    if (!/^https?:$/.test(target.protocol)) {
      return res.status(400).json({ error: 'Only http/https allowed' });
    }
    const r = await axios.get(target.toString(), {
      responseType: 'stream',
      headers: { 'User-Agent': 'IPTV-Portal/0.1' }
    });
    for (const [key, value] of Object.entries(r.headers)) {
      if (typeof value === 'string') res.setHeader(key, value);
    }
    r.data.pipe(res);
  } catch (err) {
    if (err.response) {
      res.status(err.response.status);
      if (err.response.data && err.response.data.pipe) {
        return err.response.data.pipe(res);
      }
      return res.send(err.response.data);
    }
    res.status(500).json({ error: err.message });
  }
});

const clientDist = path.join(__dirname, '../client/dist');
if (fs.existsSync(clientDist)) {
  app.use(express.static(clientDist));
  app.get('*', (req, res) => res.sendFile(path.join(clientDist, 'index.html')));
} else {
  app.get('/', (req, res) => res.send('Client not built yet. Run npm -w client run build.'));
}

app.listen(PORT, () => {
  console.log(`Server listening on http://localhost:${PORT}`);
});
