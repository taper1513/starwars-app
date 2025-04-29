import { useNavigate, useParams } from 'react-router-dom';
import DetailPage from '../components/DetailPage';
import { useApi } from '../hooks/useApi';

interface Movie {
  id: string;
  title: string;
  opening_crawl: string;
  characters: Array<{
    id: string;
    name: string;
  }>;
}

export default function MovieDetailsPage() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();

  const { data, isLoading, error } = useApi<Movie>(
    `/movies/${id}`,
    ['movie', id],
    {
      enabled: !!id,
    }
  );

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-[calc(100vh-200px)]">
        <div className="text-center">
          <p className="mt-4 text-gray-600">Loading movie details...</p>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="flex flex-col items-center justify-center h-[calc(100vh-200px)] text-center">
        <h2 className="text-2xl font-bold text-red-600 mb-4">Error Loading Movie</h2>
        <p className="text-gray-600 mb-4">{error.message}</p>
        <button
          onClick={() => navigate('/')}
          className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
        >
          Return to Search
        </button>
      </div>
    );
  }

  if (!data) {
    return (
      <div className="flex flex-col items-center justify-center h-[calc(100vh-200px)] text-center">
        <h2 className="text-2xl font-bold text-gray-600 mb-4">Movie Not Found</h2>
        <p className="text-gray-600 mb-4">The requested movie could not be found.</p>
        <button
          onClick={() => navigate('/')}
          className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
        >
          Return to Search
        </button>
      </div>
    );
  }

  return (
    <DetailPage
      title={data.title}
      subtitles={[
        { label: 'Opening Crawl', content: <div style={{ whiteSpace: 'pre-line' }}>{data.opening_crawl}</div> },
      ]}
      sideTitle="Characters"
      sideLinks={
        (data.characters || []).map((char) => ({
          id: char.id,
          label: char.name,
          onClick: () => navigate(`/people/${char.id}`),
        }))
      }
      onBack={() => navigate('/')}
    />
  );
} 