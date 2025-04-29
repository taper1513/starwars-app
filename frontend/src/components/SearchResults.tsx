import { useNavigate } from 'react-router-dom';
import { ApiError } from '../hooks/useApi';

type Result = {
    id: string;
    name?: string;
    title?: string;
  };
  
  type SearchResultsProps = {
    results: Result[];
    type: 'people' | 'movies';
    isLoading?: boolean;
    error?: ApiError;
  };
  
  export default function SearchResults({ results, type, isLoading, error }: SearchResultsProps) {
    const navigate = useNavigate();

    return (
      <div className="bg-white shadow p-8 lg:w-[582px] h-[582px] overflow-y-auto">
        <h2 className="text-2xl font-bold text-black mb-6">Results</h2>
        <div className="h-[1px] lg:w-[522px] bg-[#c4c4c4] sm:w-full"></div>
        {isLoading ? (
          <div className="flex items-center justify-center h-[450px]">
            <p className="font-montserrat text-center text-[14px] font-bold text-[#c4c4c4]">
              Searching...
            </p>
          </div>
        ) : error ? (
          <div className="flex items-center justify-center h-[450px] flex-col text-center">
            <p className="text-xl text-red-600 mb-2">{error.error}</p>
            <p className="text-gray-600">{error.message}</p>
            {error.error === 'Network Error' && (
              <button
                onClick={() => window.location.reload()}
                className="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors"
              >
                Retry
              </button>
            )}
          </div>
        ) : results.length > 0 ? (
          <ul className="flex flex-col">
            {results.map((item, index) => (
              <li key={item.id} className="relative">
                <div className="flex justify-between items-center py-[10px] h-[54px] gap-4">
                  <span className="font-montserrat text-base font-bold text-black flex-1">{item.name || item.title}</span>
                  <button
                    onClick={() => navigate(`/${type}/${item.id}`)}
                    className="h-[34px] px-5 py-2 rounded-[17px] bg-[#0ab463] text-sm text-white font-bold transition-all duration-200 hover:opacity-90 shrink-0"
                  >
                    SEE DETAILS
                  </button>
                </div>
                {index < results.length - 1 && (
                  <div className="h-[1px] w-[522px] bg-[#c4c4c4]"></div>
                )}
              </li>
            ))}
          </ul>
        ) : (
          <div className="flex items-center justify-center h-[450px] flex-col">
            <p className="font-montserrat text-center text-[14px] font-bold text-[#c4c4c4]">
              There are zero matches. <br/>
              Use the form to search for People or Movies.
            </p>
          </div>
        )}
      </div>
    );
  }