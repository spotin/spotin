import {
  Get,
  Controller,
  Render,
  UseGuards,
  Param,
  UseFilters,
  Res,
} from '@nestjs/common';
import { JwtAuthGuard } from 'src/auth/jwt/jwt-auth-guards';
import { UnauthorizedExceptionFilter } from 'src/filters/unauthorized-exception.filter';
import { SpotsService } from './spots.service';
import * as qrcode from 'qrcode';
import { Response } from 'express';

@Controller('spots')
export class SpotsViewsController {
  constructor(private readonly spotsService: SpotsService) {}

  @UseGuards(JwtAuthGuard)
  @Get('list')
  @UseFilters(new UnauthorizedExceptionFilter())
  @Render('spots/list')
  async root() {
    const spots = await this.spotsService.getSpots();
    return { spots };
  }

  @Get('latest')
  @Render('spots/latest')
  async getLatestSpots() {
    const spots = await this.spotsService.getSpots();
    return { spots };
  }

  @UseGuards(JwtAuthGuard)
  @Get(':uuid')
  @UseFilters(new UnauthorizedExceptionFilter())
  async getSpot(@Res() res: Response, @Param('uuid') uuid: string) {
    const [spot] = await this.spotsService.getSpot(uuid);

    const redirection = `http://192.168.1.183:3000/spots/${spot.uuid}/redirect`;

    if (redirection) {
      qrcode
        .toDataURL(redirection)
        .then((url) => {
          res.render('spots/[uuid]', {
            title: 'Spot',
            spot: spot,
            qrcode: url,
          });
        })
        .catch((err) => {
          console.error(err);
        });
    }
  }
}
