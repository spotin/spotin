import { Module } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { PrismaModule } from 'nestjs-prisma';
import { SpotsService } from '@/spots/spots.service';
import { SpotsController } from '@/spots/spots.controller';
import { SpotsViewsController } from '@/spots/spots-views.controller';
import { MarkdownModule } from '@/markdown/markdown.module';

@Module({
	imports: [ConfigModule, MarkdownModule, PrismaModule],
	providers: [SpotsService],
	controllers: [SpotsController, SpotsViewsController],
	exports: [SpotsService],
})
export class SpotsModule {}
