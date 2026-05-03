# 🔐 Secure File Download API

Production-style secure file download implementation using encrypted tokens and expiring requests.

> Built with Yii2 + Node.js & Python client examples

---

## 🚀 Features

- AES-256-CBC encrypted tokens  
- Expiring requests (timestamp-based)  
- Token integrity validation  
- IP whitelisting support  
- Secure CSV streaming  

---

## 🏗 How It Works

1. Client generates a timestamp  
2. Timestamp is encrypted → `token`  
3. Client sends request:
   - `yearMonth`
   - `token`
4. Server validates:
   - IP address  
   - Token integrity  
   - Expiration  
5. CSV file is streamed as response  

---

## 📡 API Endpoint

```
POST /rfm/exportirx4fghghjgh6h87eriu
```

---

## 📁 Project Structure

```
Yii2/
 └──frontend
    ├──controllers
    |  └─── RfmController.php # Security + request validation
    └──models
       └──RfmData.php         # Data + CSV generation

clients/
 ├── test.js             # Node.js example
 └── test2.py            # Python example
```

---

## ⚡ Usage (Integration Example)

This repository is a **code sample**, not a full standalone application.

### Yii2 Integration

1. Add `RfmController` to your Yii2 project  
2. Add `RfmData` model  
3. Configure:
   - Encryption key  
   - Allowed IP addresses  
   - API route  

### Run Client Examples

```bash
node clients/test.js
```

or

```bash
python clients/test2.py
```

---

## 📄 Output

- File: `rfm_data_YYYYMM.csv`  
- Delivered via: `Content-Disposition: attachment`  

---

## 🔐 Security Model

- Encrypted tokens prevent parameter tampering  
- Timestamp prevents replay attacks  
- IP whitelist restricts access  
- Hidden endpoint reduces exposure  

### Threats Mitigated

- Unauthorized file downloads  
- Token reuse attacks  
- Parameter manipulation  

---

## ⚙️ Production Considerations

- Store secrets in environment variables  
- Rotate encryption keys  
- Add rate limiting  
- Log access attempts  
- Consider JWT for scalability  

---

## 💡 Use Cases

- Secure report downloads  
- Internal analytics systems  
- Financial data exports  
- Token-based file access APIs  

---

## 🔄 Roadmap

- [ ] JWT-based authentication  
- [ ] Rate limiting  
- [ ] Signed URLs  
- [ ] Docker setup  

---

## 📜 License

MIT
