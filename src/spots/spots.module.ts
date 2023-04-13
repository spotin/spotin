import { Module } from '@nestjs/common';
import { SpotsService } from './spots.service';
import { SpotsApiController } from './spots-api.controller';
import { SpotsViewsController } from './spots-views.controller';
import { ConfigModule } from '@nestjs/config';
import { PrismaModule } from 'nestjs-prisma';

@Module({
  imports: [ConfigModule, PrismaModule],
  providers: [SpotsService],
  controllers: [SpotsApiController, SpotsViewsController],
})
export class SpotsModule {}
