import { useNavigate, useParams } from 'react-router-dom';
import DetailPage from '../components/DetailPage';
import { useApi } from '../hooks/useApi';

interface Person {
  id: string;
  name: string;
  birth_year: string | null;
  gender: string | null;
  height: string | null;
  mass: string | null;
  hair_color: string | null;
  eye_color: string | null;
  films: Array<{
    id: string;
    title: string;
  }>;
}

export default function PersonDetailsPage() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();

  const { data, isLoading, error } = useApi<Person>(
    `/people/${id}`,
    ['person', id],
    {
      enabled: !!id,
    }
  );

  if (isLoading) {
    return (
      <div className="flex items-center justify-center h-[calc(100vh-200px)]">
        <div className="text-center">
          <p className="mt-4 text-gray-600">Loading character details...</p>
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="flex flex-col items-center justify-center h-[calc(100vh-200px)] text-center">
        <h2 className="text-2xl font-bold text-red-600 mb-4">Error Loading Character</h2>
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
        <h2 className="text-2xl font-bold text-gray-600 mb-4">Character Not Found</h2>
        <p className="text-gray-600 mb-4">The requested character could not be found.</p>
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
      title={data.name}
      subtitles={[
        {
          label: 'Attributes',
          content: (
            <ul>
              <li><b>Birth Year:</b> {data.birth_year || 'Unknown'}</li>
              <li><b>Gender:</b> {data.gender || 'Unknown'}</li>
              <li><b>Height:</b> {data.height || 'Unknown'}</li>
              <li><b>Mass:</b> {data.mass || 'Unknown'}</li>
              <li><b>Hair Color:</b> {data.hair_color || 'Unknown'}</li>
              <li><b>Eye Color:</b> {data.eye_color || 'Unknown'}</li>
            </ul>
          ),
        },
      ]}
      sideTitle="Movies"
      sideLinks={
        (data.films || []).map((film) => ({
          id: film.id,
          label: film.title,
          onClick: () => navigate(`/movies/${film.id}`),
        }))
      }
      onBack={() => navigate('/')}
    />
  );
} 