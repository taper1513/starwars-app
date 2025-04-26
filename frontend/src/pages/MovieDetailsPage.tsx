import { useNavigate, useParams } from 'react-router-dom';
import { useQuery } from '@tanstack/react-query';
import axios from 'axios';
import DetailPage from '../components/DetailPage';

export default function MovieDetailsPage() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();

  const { data, isLoading, error } = useQuery({
    queryKey: ['movie', id],
    queryFn: async () => {
      const res = await axios.get(`http://localhost:8000/api/movies/${id}`);
      return res.data;
    },
    enabled: !!id,
  });

  if (isLoading) return <div className="text-center mt-10">Loading...</div>;
  if (error || !data) return <div className="text-center mt-10">Movie not found.</div>;

  return (
    <DetailPage
      title={data.title}
      subtitles={[
        { label: 'Opening Crawl', content: <div style={{ whiteSpace: 'pre-line' }}>{data.opening_crawl}</div> },
      ]}
      sideTitle="Characters"
      sideLinks={
        (data.characters || []).map((char: any) => ({
          id: char.id,
          label: char.name,
          onClick: () => navigate(`/people/${char.id}`),
        }))
      }
      onBack={() => navigate('/')}
    />
  );
} 