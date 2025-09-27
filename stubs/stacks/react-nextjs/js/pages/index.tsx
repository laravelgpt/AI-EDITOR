import { useEffect } from 'react'
import { useRouter } from 'next/router'
import { useSelector, useDispatch } from 'react-redux'
import { RootState } from '../store'
import { checkAuth } from '../store/slices/authSlice'
import Dashboard from '../components/Dashboard'
import Login from '../components/Login'

export default function Home() {
  const router = useRouter()
  const dispatch = useDispatch()
  const { isAuthenticated, loading } = useSelector((state: RootState) => state.auth)

  useEffect(() => {
    dispatch(checkAuth())
  }, [dispatch])

  useEffect(() => {
    if (!loading && !isAuthenticated) {
      router.push('/login')
    }
  }, [isAuthenticated, loading, router])

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-500"></div>
      </div>
    )
  }

  if (!isAuthenticated) {
    return <Login />
  }

  return <Dashboard />
}
