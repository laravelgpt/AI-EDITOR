import { useEffect } from 'react'
import { useDispatch, useSelector } from 'react-redux'
import { RootState } from '../store'
import { fetchDashboardData } from '../store/slices/dashboardSlice'
import StatsCards from './StatsCards'
import RecentActivity from './RecentActivity'
import QuickActions from './QuickActions'

export default function Dashboard() {
  const dispatch = useDispatch()
  const { data, loading, error } = useSelector((state: RootState) => state.dashboard)

  useEffect(() => {
    dispatch(fetchDashboardData())
  }, [dispatch])

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="animate-spin rounded-full h-32 w-32 border-b-2 border-blue-500"></div>
      </div>
    )
  }

  if (error) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <h2 className="text-2xl font-bold text-gray-900 mb-4">Error Loading Dashboard</h2>
          <p className="text-gray-600">{error}</p>
        </div>
      </div>
    )
  }

  return (
    <div className="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
      {/* Header */}
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p className="mt-2 text-gray-600">Welcome back! Here's what's happening today.</p>
      </div>

      {/* Stats Cards */}
      <StatsCards data={data?.stats} />

      {/* Main Content Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
        {/* Recent Activity */}
        <RecentActivity activities={data?.activities} />

        {/* Quick Actions */}
        <QuickActions />
      </div>
    </div>
  )
}
