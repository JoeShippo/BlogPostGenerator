
# Blog Post Generator

This PHP web application takes CSV uploads containing topics and generates detailed blog posts using OpenAI's GPT-4. Each blog post is formatted and saved as a Word document. The app is designed to assist content creators by automating the generation of high-quality, structured content.

## Features

- **CSV Upload**: Users can upload a CSV file containing blog post topics.
- **Content Generation**: Leverages OpenAI's GPT-4 to generate engaging and detailed blog posts based on the topics provided.
- **Word Document Creation**: Each blog post is automatically formatted and saved as a Microsoft Word document.
- **Lightweight Interface**: Utilizes Pico CSS for a minimalistic and responsive user interface.

## Installation

To set up the project locally, follow these steps:

### Prerequisites

- PHP 7.4 or higher
- Composer for managing PHP dependencies

### Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/yourusername/blog-post-generator.git
   cd blog-post-generator
   ```

2. **Install Dependencies**
   ```bash
   composer install
   ```

3. **Configuration**
   - Copy `config.sample.php` to `config.php` and adjust the settings, particularly the OpenAI API key.
   ```php
   define('OPENAI_API_KEY', 'your-openai-api-key');
   ```

4. **Run the Application**
   - Use a local server like XAMPP or MAMP and navigate to the project directory in your web browser.

## Usage

1. **Prepare Your CSV File**: Ensure your CSV file contains a single column with blog topics, one per line.
2. **Upload the CSV**: Use the form on the homepage to upload your CSV file and specify any necessary context or calls to action.
3. **Download Generated Documents**: After processing, download the generated Word documents from the results page.

## Contributing

Contributions to the Blog Post Generator are welcome! Please follow these steps to contribute:

1. Fork the repository.
2. Create a new branch (`git checkout -b feature/your-feature`).
3. Commit your changes (`git commit -am 'Add some feature'`).
4. Push to the branch (`git push origin feature/your-feature`).
5. Open a new Pull Request.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments

- OpenAI for providing the GPT-4 API.
- Pico CSS for the responsive CSS framework.
