import { useState, FormEvent } from 'react';
import { CustomRadio } from './CustomRadio';

type Props = {
  onSearch: (params: { query: string; type: string }) => void;
};

export default function SearchForm({ onSearch }: Props) {
  const [query, setQuery] = useState('');
  const [type, setType] = useState<'people' | 'movies'>('people');

  const handleSubmit = (e: FormEvent) => {
    e.preventDefault();
    if (!query.trim()) return;
    onSearch({ query: query.trim(), type });
  };

  const isButtonDisabled = !query.trim();

  return (
    <form onSubmit={handleSubmit} className="flex flex-col gap-2 p-8 bg-white shadow w-full  lg:max-w-[410px]">
      <span className="font-montserrat text-sm font-semibold text-primary">
        What are you searching for?
      </span>
      <div className="flex gap-8">
        <CustomRadio
          checked={type === 'people'}
          onChange={() => setType('people')}
          label="People"
          name="search-type"
          value="people"
        />
        <CustomRadio
          checked={type === 'movies'}
          onChange={() => setType('movies')}
          label="Movies"
          name="search-type"
          value="movies"
        />
      </div>
      <div className="flex flex-col gap-4">
        <input
          type="text"
          placeholder="e.g. Chewbacca, Yoda, Boba Fett"
          value={query}
          onChange={(e) => setQuery(e.target.value)}
          className="w-[350px] h-[40px] shadow-input border-[1px] rounded-sm border-gray-200 border-[#c4c4c4] 
            px-6 py-3 text-lg focus:outline-none focus:border-[#383838] focus:border-[1px]"
             
        />
        <button
          type="submit"
          disabled={isButtonDisabled}
          className={`
            rounded
            transition-all duration-200 shadow
            ${isButtonDisabled
              ? 'bg-[#c4c4c4] border border-[#c4c4c4] h-9 w-full font-montserrat font-bold text-white cursor-not-allowed'
              : 'bg-green-500 hover:bg-green-600 h-9 w-full text-white font-bold'
            }
          `}
        >
          SEARCH
        </button>
      </div>
    </form>
  );
}