import React, { useEffect, useState } from 'react'
import { Link, useNavigate } from 'react-router-dom'

export default function Users(){
  const [users, setUsers] = useState([])
  const [loading, setLoading] = useState(true)
  const navigate = useNavigate()

  useEffect(()=>{
    fetch('/api/users').then(r=>r.json()).then(setUsers).finally(()=>setLoading(false))
  },[])

  async function remove(id){
    if(!confirm('Remove this user?')) return
    await fetch(`/api/users/${id}`,{method:'DELETE'})
    setUsers(prev=>prev.filter(u=>u.id!==id))
  }

  return (
    <div className="container" style={{paddingTop:24, alignItems:'flex-start'}}>
      <div className="left" style={{alignItems:'flex-start'}}>
        <Link className="nav-back" to="/">← Back</Link>
        <h2 style={{marginTop:0}}>Users</h2>
        <button className="action" onClick={()=>navigate('/')}>＋ Add another</button>
      </div>
      <div className="card">
        <table className="table">
          <thead>
            <tr>
              <th>Name</th>
              <th>MAC</th>
              <th>Portal URL</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            {loading? (
              <tr><td colSpan="4">Loading…</td></tr>
            ) : users.length===0 ? (
              <tr><td colSpan="4">No users yet</td></tr>
            ) : users.map(u=> (
              <tr key={u.id}>
                <td>{u.name}</td>
                <td><span className="badge">{u.mac}</span></td>
                <td>{u.portalUrl}</td>
                <td>
                  <button className="button-primary" onClick={()=>navigate(`/player/${u.id}`)} style={{marginRight:8}}>Open</button>
                  <button className="button-primary" onClick={()=>remove(u.id)} style={{background:'#4b3a82'}}>Delete</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  )
}
