import { useNavigate, useParams } from 'react-router-dom';
import { useQuery } from '@tanstack/react-query';
import axios from 'axios';
import DetailPage from '../components/DetailPage';

export default function PersonDetailsPage() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();

  const { data, isLoading, error } = useQuery({
    queryKey: ['person', id],
    queryFn: async () => {
      const res = await axios.get(`http://localhost:8000/api/people/${id}`);
      return res.data;
    },
    enabled: !!id,
  });

  if (isLoading) return <div className="text-center mt-10">Loading...</div>;
  if (error || !data) return <div className="text-center mt-10">Person not found.</div>;

  return (
      <DetailPage
        title={data.name}
        subtitles={[
          { label: 'Attributes', content: (
            <ul>
              <li><b>Birth Year:</b> {data.birth_year}</li>
              <li><b>Gender:</b> {data.gender}</li>
              <li><b>Height:</b> {data.height}</li>
              <li><b>Mass:</b> {data.mass}</li>
              <li><b>Hair Color:</b> {data.hair_color}</li>
              <li><b>Eye Color:</b> {data.eye_color}</li>
            </ul>
          ) },
        ]}
        sideTitle="Movies"
        sideLinks={
          (data.films || []).map((film: any) => ({
            id: film.id,
            label: film.title,
            onClick: () => navigate(`/movies/${film.id}`),
          }))
        }
        onBack={() => navigate('/')}
      />
  );
} 