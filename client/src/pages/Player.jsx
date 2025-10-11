import React, { useEffect, useMemo, useRef, useState } from 'react'
import { Link, useParams } from 'react-router-dom'
import Hls from 'hls.js'

export default function Player(){
  const { id } = useParams()
  const [user, setUser] = useState(null)
  const [channels, setChannels] = useState([])
  const videoRef = useRef(null)

  useEffect(()=>{
    fetch(`/api/users/${id}`).then(r=>r.json()).then(setUser)
  },[id])

  useEffect(()=>{
    // For demo, load sample channels. In real setup, use user.portalUrl
    fetch('/api/proxy?url=' + encodeURIComponent('http://localhost:'+location.port+'/api/mock-channels')).then(res=>res.json()).then(setChannels).catch(()=>{
      // Fallback to static
      fetch('/api/mock-channels').then(r=>r.json()).then(setChannels)
    })
  },[])

  function play(url){
    const video = videoRef.current
    if (!video) return
    if (Hls.isSupported()){
      const hls = new Hls()
      hls.loadSource(url)
      hls.attachMedia(video)
    } else if (video.canPlayType('application/vnd.apple.mpegurl')){
      video.src = url
    } else {
      alert('HLS not supported in this browser')
    }
  }

  return (
    <div className="container" style={{paddingTop:24, alignItems:'flex-start'}}>
      <div className="left" style={{alignItems:'flex-start'}}>
        <Link className="nav-back" to="/users">← Back</Link>
        <h2 style={{marginTop:0}}>{user? user.name : 'Loading…'}</h2>
        <div className="badge">{user? user.mac : ''}</div>
      </div>
      <div className="card" style={{width:'70%'}}>
        <div className="player">
          <video ref={videoRef} className="video" controls playsInline autoPlay muted></video>
          <div className="sidebar">
            <h3>Channels</h3>
            {channels.map((c,i)=> (
              <div className="channel" key={i} onClick={()=>play(c.url)}>{c.name}</div>
            ))}
          </div>
        </div>
      </div>
    </div>
  )
}
