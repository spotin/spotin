import { Module } from '@nestjs/common';
import { SpotsService } from './spots.service';
import { SpotsController } from './spots.controller';

@Module({
  providers: [SpotsService],
  controllers: [SpotsController],
})
export class SpotsModule {}
