import { MarkdownService } from '@/markdown/markdown.service';
import { Module } from '@nestjs/common';

@Module({
	imports: [],
	providers: [MarkdownService],
	exports: [MarkdownService],
})
export class MarkdownModule {}
