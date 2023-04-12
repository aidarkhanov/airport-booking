import { useState } from 'react'
import { useQuery, useQueryClient, useMutation } from 'react-query'

function App() {
  const { isLoading, error, data } = useQuery('name', () =>
    fetch('http://api.airport-booking.local/v1/hello/Dair').then(res => res.json()))

  if (isLoading) return 'Loading...'

  if (error) return 'An error has occurred: ' + error.message

  return (
    <p>{data.name}</p>
  )
}

export default App
