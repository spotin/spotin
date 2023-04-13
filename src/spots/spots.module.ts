import { Module } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { PrismaModule } from 'nestjs-prisma';
import { SpotsService } from '@/spots/spots.service';
import { SpotsApiController } from '@/spots/spots-api.controller';
import { SpotsViewsController } from '@/spots/spots-views.controller';

@Module({
  imports: [ConfigModule, PrismaModule],
  providers: [SpotsService],
  controllers: [SpotsApiController, SpotsViewsController],
})
export class SpotsModule {}
