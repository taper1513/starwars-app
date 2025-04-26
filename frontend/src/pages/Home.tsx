import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import LayoutContainer from '../layouts/LayoutContainer';
import SearchForm from '../components/SearchForm';
import SearchResults from '../components/SearchResults';
import axios from 'axios';

type Result = {
  id: string;
  name?: string;
  title?: string;
};

export default function HomePage() {
  const [searchParams, setSearchParams] = useState<{ query: string; type: string } | null>(null);

  const { data = [], isLoading } = useQuery({
    queryKey: ['search', searchParams],
    queryFn: async () => {
      if (!searchParams) return [];
      const response = await axios.get<{ results: Result[] }>('http://localhost:8000/api/search', {
        params: {
          type: searchParams.type,
          query: searchParams.query,
        },
      });
      return response.data.results;
    },
    enabled: !!searchParams,
  });

  return (
    <LayoutContainer>
      <div className="flex flex-col lg:flex-row gap-12 justify-center items-start w-full max-w-6xl">
        <div className="w-full flex-1">
          <SearchForm onSearch={setSearchParams} />
        </div>

        <div className="flex-[2] w-full">
          {isLoading ? (
            <div className="text-gray-600 mb-4">Loading...</div>
          ) : (
            <SearchResults results={data} onSelect={(item) => console.log('Selected:', item)} />
          )}
        </div>
      </div>
    </LayoutContainer>
  );
}
