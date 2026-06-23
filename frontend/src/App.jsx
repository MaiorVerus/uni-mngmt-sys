
import { BrowserRouter, Route, Routes } from "react-router-dom"

import Register from "./pages/register/register"
import LandingPage from "./pages/landing/landing-pg"
function App() {

  
  return (
    <BrowserRouter>
    <Routes>
      <Route path="/" element={<LandingPage/>}/>
      <Route path="/register" element={<Register/>}/>

    </Routes>
    </BrowserRouter>
    
  )
}

export default App
