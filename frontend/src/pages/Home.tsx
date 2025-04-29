import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import SearchForm from '../components/SearchForm';
import SearchResults from '../components/SearchResults';
import axios from 'axios';
import { ApiError } from '../hooks/useApi';

type Result = {
  id: string;
  name?: string;
  title?: string;
};

export default function HomePage() {
  const [searchParams, setSearchParams] = useState<{ query: string; type: string } | null>(null);

  const { data = [], isLoading, error } = useQuery<Result[], ApiError>({
    queryKey: ['search', searchParams],
    queryFn: async () => {
      if (!searchParams) return [];
      const response = await axios.get<{ results: Result[] }>('http://localhost:8000/api/search', {
        params: {
          type: searchParams.type,
          query: searchParams.query,
        },
      });
      return response.data.results ?? [];
    },
    enabled: !!searchParams,
    staleTime: 1000 * 60 * 5,
  });

  return (
    <div className="flex flex-col lg:flex-row gap-8 justify-center items-start w-full max-w-6xl">
      <div className="w-full lg:flex-[3] lg:place-items-end">
        <SearchForm onSearch={setSearchParams} />
      </div>

      <div className="flex-[4] w-full">
        <SearchResults 
          results={data} 
          type={searchParams?.type as 'people' | 'movies'} 
          isLoading={isLoading}
          error={error || undefined}
        />
      </div>
    </div>
  );
}
