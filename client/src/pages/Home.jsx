import React, { useState } from 'react'
import { useNavigate } from 'react-router-dom'

export default function Home(){
  const [name, setName] = useState('')
  const [mac, setMac] = useState('')
  const [portalUrl, setPortalUrl] = useState('')
  const [submitting, setSubmitting] = useState(false)
  const navigate = useNavigate()

  async function addUser(e){
    e.preventDefault()
    setSubmitting(true)
    try{
      const res = await fetch('/api/users',{method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({name, mac, portalUrl})})
      const data = await res.json()
      if(!res.ok){
        alert(data.error || 'Failed to add user')
      }else{
        navigate('/users')
      }
    }finally{
      setSubmitting(false)
    }
  }

  return (
    <div className="container">
      <div className="left">
        <div className="logo"><div className="logo-text">VU</div></div>
        <div className="brand">IPTV Player</div>
        <div className="premium">PREMIUM</div>
        <button className="action" onClick={()=>navigate('/users')}>📋 List of User</button>
      </div>
      <div className="card">
        <h2>Stalker Portal Connectivity</h2>
        <form onSubmit={addUser}>
          <div className="form-row">
            <input className="input" placeholder="Name" value={name} onChange={e=>setName(e.target.value)} />
            <input className="input" placeholder="MAC Address" value={mac} onChange={e=>setMac(e.target.value)} />
            <input className="input" placeholder="Portal URL" value={portalUrl} onChange={e=>setPortalUrl(e.target.value)} />
            <button className="button-primary" disabled={submitting}>{submitting? 'ADDING...' : 'ADD USER'}</button>
          </div>
        </form>
        <div className="note">
          Note - Do not connect any content that infringes copyrights on the application. We do not sell any playlist or subscriptions. VU IPTV Player is a General Media Player that does not include any content.
        </div>
      </div>
    </div>
  )
}
