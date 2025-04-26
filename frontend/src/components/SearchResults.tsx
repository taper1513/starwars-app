type Result = {
    id: string;
    name?: string;
    title?: string;
  };
  
  type SearchResultsProps = {
    results: Result[];
    onSelect: (item: Result) => void;
  };
  
  export default function SearchResults({ results, onSelect }: SearchResultsProps) {
    return (
      <div className="bg-white shadow p-8 w-full">
        <h2 className="text-2xl font-bold text-green-600 mb-6">Results</h2>
        {results.length > 0 ? (
          <ul className="flex flex-col gap-4">
            {results.map((item) => (
              <li key={item.id} className="flex justify-between items-center py-4 border-b border-gray-100 last:border-0">
                <span className="text-xl font-medium text-gray-800">{item.name || item.title}</span>
                <button
                  onClick={() => onSelect(item)}
                  className="rounded bg-green-500 text-white font-bold py-3 px-6 hover:bg-green-600 transition-all duration-200 shadow"
                >
                  SEE DETAILS
                </button>
              </li>
            ))}
          </ul>
        ) : (
          <div className="text-center py-8">
            <p className="text-xl text-gray-500 mb-2">There are zero matches.</p>
            <p className="text-gray-400">Use the form to search for People or Movies.</p>
          </div>
        )}
      </div>
    );
  }