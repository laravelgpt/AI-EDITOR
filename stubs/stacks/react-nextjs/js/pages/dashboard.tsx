import { useEffect } from 'react'
import { useRouter } from 'next/router'
import { useSelector, useDispatch } from 'react-redux'
import { RootState } from '../store'
import { fetchDashboardData } from '../store/slices/dashboardSlice'
import Layout from '../components/Layout'
import StatsCards from '../components/StatsCards'
import RecentActivity from '../components/RecentActivity'
import QuickActions from '../components/QuickActions'

export default function DashboardPage() {
  const router = useRouter()
  const dispatch = useDispatch()
  const { isAuthenticated } = useSelector((state: RootState) => state.auth)
  const { data, loading, error } = useSelector((state: RootState) => state.dashboard)

  useEffect(() => {
    if (!isAuthenticated) {
      router.push('/login')
      return
    }

    dispatch(fetchDashboardData())
  }, [dispatch, isAuthenticated, router])

  if (!isAuthenticated) {
    return null
  }

  return (
    <Layout>
      <div className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-3xl font-bold text-gray-900">Dashboard</h1>
          <p className="mt-2 text-gray-600">Welcome back! Here's what's happening today.</p>
        </div>

        {/* Stats Cards */}
        <StatsCards data={data?.stats} loading={loading} />

        {/* Main Content Grid */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
          {/* Recent Activity */}
          <RecentActivity activities={data?.activities} loading={loading} />

          {/* Quick Actions */}
          <QuickActions />
        </div>
      </div>
    </Layout>
  )
}
